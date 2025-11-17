from fastapi import APIRouter, Depends, BackgroundTasks, HTTPException, status
from typing import Optional
from sqlalchemy.orm import Session, joinedload
import socket
from hl7apy.parser import parse_message
from sqlalchemy import func, and_
from app.models.lab_device import DeviceMessage as DeviceMessageModel
from app.schemas.lab_device import LabDeviceCreate, LabDevice, LabDeviceUpdate
from app.crud.lab_device import get_device, get_devices, create_device, create_fhir_resource
from app.database import get_db
from app.routers.auth import get_current_user
from app.models.user import User
from app.crud.lab_result import create_lab_result
from app.schemas.lab_result import LabResultCreate
from app.models.patient import Patient
from app.models.analysis import NormalRange, AnalysisCatalog
from app.schemas.lab_result import LabResultCreate
from datetime import datetime

router = APIRouter(prefix="/lab-devices", tags=["Lab Devices"])


# ==================== MLLP Helper Functions ====================
def mllp_wrap(message: str) -> bytes:
    """Wrap HL7 message with MLLP start and end block characters."""
    return b'\x0b' + message.encode('utf-8') + b'\x1c\r'


def mllp_unwrap(data: bytes) -> str:
    """Safely unwrap MLLP, preserving HL7 structure and carriage returns."""
    START_BLOCK = b'\x0b'
    END_BLOCK = b'\x1c\r'

    if data.startswith(START_BLOCK):
        data = data[len(START_BLOCK):]
    if data.endswith(END_BLOCK):
        data = data[:-len(END_BLOCK)]

    return data.decode('utf-8', errors='ignore')


# ==================== Device CRUD (Basic Routes) ====================

@router.post("/", response_model=LabDevice, status_code=201)
def create_lab_device(device: LabDeviceCreate, db: Session = Depends(get_db)):
    """Create a new lab device"""
    return create_device(db, device)


@router.get("/", response_model=list[LabDevice])
def list_lab_devices(
    skip: int = 0, 
    limit: int = 100,
    status: Optional[str] = None,
    manufacturer: Optional[str] = None,
    is_active: Optional[bool] = None,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    """List all lab devices with optional filters"""
    from app.models.lab_device import LabDevice as LabDeviceModel
    
    query = db.query(LabDeviceModel)
    
    if status:
        query = query.filter(LabDeviceModel.status == status)
    if manufacturer:
        query = query.filter(LabDeviceModel.manufacturer == manufacturer)
    if is_active is not None:
        query = query.filter(LabDeviceModel.is_active == is_active)
    
    return query.offset(skip).limit(limit).all()


# ⚠️ SPECIFIC ROUTES MUST COME BEFORE /{device_id}

@router.get("/summary")
def get_devices_summary(db: Session = Depends(get_db)):
    """Get quick summary of all devices"""
    from app.models.lab_device import LabDevice as LabDeviceModel
    
    devices = db.query(LabDeviceModel).all()
    
    return {
        "total": len(devices),
        "online": sum(1 for d in devices if d.status == 'online'),
        "offline": sum(1 for d in devices if d.status == 'offline'),
        "error": sum(1 for d in devices if d.status == 'error'),
        "active": sum(1 for d in devices if d.is_active),
        "by_manufacturer": {
            manufacturer: sum(1 for d in devices if d.manufacturer == manufacturer)
            for manufacturer in set(d.manufacturer for d in devices if d.manufacturer)
        },
        "by_protocol": {
            protocol: sum(1 for d in devices if d.protocol_type == protocol)
            for protocol in set(d.protocol_type for d in devices if d.protocol_type)
        }
    }


@router.post("/send_all_orders")
async def send_hl7_order_to_all_devices(
    background_tasks: BackgroundTasks, 
    db: Session = Depends(get_db)
):
    """Send order to all devices"""
    devices = get_devices(db)
    if not devices:
        raise HTTPException(status_code=404, detail="No devices found")

    timestamp = datetime.now().strftime('%Y%m%d%H%M%S')
    hl7_order_message = (
        f"MSH|^~\\&|LIS|HOSPITAL|DEVICE|LAB|{timestamp}||ORM^O01|MSG{timestamp}|P|2.3.1\r"
        "PID|||123456||DOE^JOHN\r"
        "ORC|NW|1001\r"
        "OBR|1|||GLU^Glucose Test\r"
    )

    for device in devices:
        if device.ip_address and device.tcp_port:  # Only send to configured devices
            background_tasks.add_task(
                tcp_client_send_receive_store,
                device.ip_address,
                device.tcp_port,
                hl7_order_message,
                device.name,
                db
            )
    
    return {"status": f"HL7 orders sent to {len(devices)} devices in background"}


#  /{device_id} ROUTES

@router.get("/{device_id}", response_model=LabDevice)
def get_lab_device(device_id: int, db: Session = Depends(get_db)):
    """Get a specific device by ID"""
    device = get_device(db, device_id)
    if not device:
        raise HTTPException(status_code=404, detail="Device not found")
    return device


@router.patch("/{device_id}", response_model=LabDevice)
def update_device(
    device_id: int,
    device_update: LabDeviceUpdate,
    db: Session = Depends(get_db)
):
    """Update device configuration"""
    from app.models.lab_device import LabDevice as LabDeviceModel
    
    device = db.query(LabDeviceModel).filter(LabDeviceModel.id == device_id).first()
    if not device:
        raise HTTPException(status_code=404, detail="Device not found")
    
    update_data = device_update.dict(exclude_unset=True)
    for field, value in update_data.items():
        setattr(device, field, value)
    
    db.commit()
    db.refresh(device)
    return device


@router.delete("/{device_id}", status_code=204)
def delete_device(device_id: int, db: Session = Depends(get_db)):
    """Delete a device"""
    from app.models.lab_device import LabDevice as LabDeviceModel
    
    device = db.query(LabDeviceModel).filter(LabDeviceModel.id == device_id).first()
    if not device:
        raise HTTPException(status_code=404, detail="Device not found")
    
    db.delete(device)
    db.commit()
    return None


@router.post("/{device_id}/test_connection")
async def test_device_connection(device_id: int, db: Session = Depends(get_db)):
    """Test basic TCP connectivity to device"""
    device = get_device(db, device_id)
    if not device:
        raise HTTPException(status_code=404, detail="Device not found")
    
    # Get the actual database model object for updating
    from app.models.lab_device import LabDevice as LabDeviceModel
    db_device = db.query(LabDeviceModel).filter(LabDeviceModel.id == device_id).first()
    
    if not db_device:
        raise HTTPException(status_code=404, detail="Device not found in database")
    
    # Check if device connects to us (tcp_client)
    if device.connection_type == 'tcp_client':
        return {
            "status": "info",
            "message": f"Device '{device.name}' connects to us. Start listening on port {device.tcp_port}",
            "note": "Cannot test client connections - device must initiate"
        }
    
    # Test connection if we connect to device (tcp_server)
    if not device.ip_address or not device.tcp_port:
        raise HTTPException(status_code=400, detail="Device has no IP address or port configured")
    
    try:
        with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as sock:
            sock.settimeout(device.connection_timeout or 5)
            sock.connect((device.ip_address, device.tcp_port))
            
            # Success → Update device status to online
            db_device.status = 'online'
            db_device.last_connected = datetime.now()
            db_device.last_error = None
            db.commit()

            return {
                "status": "success",
                "message": f"Successfully connected to {device.name}",
                "ip": device.ip_address,
                "port": device.tcp_port
            }
            
    except socket.timeout as e:
        # Failure → Update device status to offline
        db_device.status = 'offline'
        db_device.last_error = f"Connection timeout: {str(e)}"
        db_device.last_disconnected = datetime.now()
        db.commit()
        
        return {
            "status": "error", 
            "message": f"Connection timeout to {device.name}"
        }
        
    except ConnectionRefusedError as e:
        # Failure → Update device status to offline
        db_device.status = 'offline'
        db_device.last_error = f"Connection refused: {str(e)}"
        db_device.last_disconnected = datetime.now()
        db.commit()
        
        return {
            "status": "error", 
            "message": f"Connection refused by {device.name}"
        }
        
    except Exception as e:
        # Failure → Update device status to error
        db_device.status = 'error'
        db_device.last_error = str(e)
        db_device.last_disconnected = datetime.now()
        db.commit()
        
        return {
            "status": "error", 
            "message": f"Failed to connect: {str(e)}"
        }


@router.post("/{device_id}/send_hl7_order")
async def send_hl7_order_to_device(
    device_id: int, 
    background_tasks: BackgroundTasks, 
    db: Session = Depends(get_db)
):
    """Send HL7 order message to specific device"""
    device = get_device(db, device_id)
    if not device:
        raise HTTPException(status_code=404, detail="Device not found")
    
    if not device.ip_address or not device.tcp_port:
        raise HTTPException(status_code=400, detail="Device has no IP address or port configured")

    timestamp = datetime.now().strftime('%Y%m%d%H%M%S')
    hl7_order_message = (
        f"MSH|^~\\&|LIS|HOSPITAL|{device.model or 'DEVICE'}|LAB|{timestamp}||ORM^O01|MSG{timestamp}|P|{device.hl7_version or '2.3.1'}\r"
        "PID|1||123456||DOE^JOHN\r"
        "ORC|NW|1001\r"
        "OBR|1|||GLU^Glucose Test\r"
    )

    background_tasks.add_task(
        tcp_client_send_receive_store,
        device.ip_address,
        device.tcp_port,
        hl7_order_message,
        device.name,
        db
    )
    
    return {
        "status": "queued",
        "message": f"HL7 order sent to device {device.name} in background",
        "device_id": device_id
    }


@router.get("/{device_id}/stats")
def get_device_stats(device_id: int, db: Session = Depends(get_db)):
    """Get message statistics for device"""
    device = get_device(db, device_id)
    if not device:
        raise HTTPException(status_code=404, detail="Device not found")
    
    today = datetime.now().date()
    
    total = db.query(func.count(DeviceMessageModel.id)).filter(
        DeviceMessageModel.device_id == device_id
    ).scalar() or 0
    
    today_count = db.query(func.count(DeviceMessageModel.id)).filter(
        and_(
            DeviceMessageModel.device_id == device_id,
            func.date(DeviceMessageModel.created_at) == today
        )
    ).scalar() or 0
    
    pending = db.query(func.count(DeviceMessageModel.id)).filter(
        and_(
            DeviceMessageModel.device_id == device_id,
            DeviceMessageModel.status == 'pending'
        )
    ).scalar() or 0
    
    failed = db.query(func.count(DeviceMessageModel.id)).filter(
        and_(
            DeviceMessageModel.device_id == device_id,
            DeviceMessageModel.status == 'failed'
        )
    ).scalar() or 0
    
    last_msg = db.query(DeviceMessageModel.created_at).filter(
        DeviceMessageModel.device_id == device_id
    ).order_by(DeviceMessageModel.created_at.desc()).first()
    
    return {
        "total_messages": total,
        "messages_today": today_count,
        "pending_messages": pending,
        "failed_messages": failed,
        "last_message_time": last_msg[0] if last_msg else None
    }


@router.get("/{device_id}/messages")
def get_device_messages(
    device_id: int,
    direction: Optional[str] = None,
    status: Optional[str] = None,
    limit: int = 50,
    db: Session = Depends(get_db)
):
    """Get message logs for a device"""
    query = db.query(DeviceMessageModel).filter(DeviceMessageModel.device_id == device_id)
    
    if direction:
        query = query.filter(DeviceMessageModel.direction == direction)
    if status:
        query = query.filter(DeviceMessageModel.status == status)
    
    messages = query.order_by(DeviceMessageModel.created_at.desc()).limit(limit).all()
    return messages


# ==================== Background Task Function ====================
def safe_parse_hl7(response: str):
    """Parse HL7 safely, adding MSH if missing."""
    if not response.strip().startswith("MSH|"):
        timestamp = datetime.now().strftime("%Y%m%d%H%M%S")
        response = (
            f"MSH|^~\\&|DEVICE|LAB|LIS|HOSPITAL|{timestamp}||ACK^O01|AUTO{timestamp}|P|2.3.1\r"
            + response
        )

    try:
        return parse_message(response)
    except Exception as e:
        raise ValueError(f"HL7 parsing failed after patch: {e}")

def tcp_client_send_receive_store(
    device_ip: str, 
    device_port: int, 
    message: str, 
    device_name: str, 
    db: Session
):
    """Your existing function - unchanged"""
    try:
        with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as sock:
            sock.settimeout(15)
            sock.connect((device_ip, device_port))

            sock.sendall(mllp_wrap(message))
            print(f"[{datetime.now()}] 📤 Sent HL7 message to {device_name} ({device_ip}:{device_port})")

            buffer = b""
            messages_received = 0
            max_messages = 2  # Expect ACK + ORU
            
            while messages_received < max_messages:
                data = sock.recv(4096)
                if not data:
                    break
                buffer += data

                # Process all complete messages in buffer
                while b'\x1c\r' in buffer:
                    end_idx = buffer.index(b'\x1c\r') + 2
                    msg_data = buffer[:end_idx]
                    buffer = buffer[end_idx:]
                    
                    response = mllp_unwrap(msg_data)
                    print(f"[{datetime.now()}] 📥 Received HL7 response ({len(response)} bytes):\n{response}\n---")

                    try:
                        hl7_msg = safe_parse_hl7(response)
                        msg_type = hl7_msg.segment('MSH').message_type.value
                        print(f"🧾 Parsed Message Type → {msg_type}")

                        if msg_type.startswith('ACK'):
                            msa_seg = hl7_msg.segment('MSA')
                            ack_code = msa_seg.acknowledgment_code.value if msa_seg else '?'
                            print(f"✅ Received ACK → Code: {ack_code}")
                            messages_received += 1

                        elif msg_type.startswith('ORU'):
                            print("🧬 Processing ORU^R01 observation result...")
                            pid_seg = hl7_msg.segment('PID')
                            patient_identifier = (
                                pid_seg.patient_id.patient_id.value if pid_seg and pid_seg.patient_id else None
                            )
                            print(f"👤 PID: {patient_identifier or 'Unknown'}")

                            patient = None
                            if patient_identifier:
                                patient = db.query(Patient).filter(Patient.file_number == patient_identifier).first()
                            if not patient:
                                print(f"⚠️ No patient found for PID {patient_identifier}")
                                messages_received += 1
                                continue
                            
                            print(f"✅ Found patient {patient.first_name} {patient.last_name}")

                            quotation = db.query(Quotation).filter(
                                Quotation.patient_id == patient.id
                            ).order_by(Quotation.created_at.desc()).first()
                            
                            if not quotation:
                                print(f"⚠️ No quotation found for {patient.file_number}")
                                messages_received += 1
                                continue

                            for obx in hl7_msg.segments('OBX'):
                                try:
                                    test_code = obx.observation_identifier.identifier.value
                                    result_value = obx.observation_value[0].value
                                    print(f"🧪 OBX → {test_code}: {result_value}")

                                    analysis = db.query(AnalysisCatalog).filter(
                                        AnalysisCatalog.code == test_code
                                    ).first()
                                    
                                    if not analysis:
                                        print(f"⚠️ Unknown analysis code {test_code}")
                                        continue

                                    quotation_item = db.query(QuotationItem).filter(
                                        QuotationItem.quotation_id == quotation.id,
                                        QuotationItem.analysis_id == analysis.id
                                    ).first()

                                    if not quotation_item:
                                        print(f"⚠️ No quotation item found for {analysis.name}")
                                        continue

                                    normal_range = db.query(NormalRange).filter(
                                        NormalRange.analysis_id == analysis.id
                                    ).first()

                                    new_result = LabResultCreate(
                                        quotation_id=quotation.id,
                                        quotation_item_id=quotation_item.id,
                                        result_value=result_value,
                                    )

                                    stored_result = create_lab_result(db, new_result, patient, normal_range)
                                    print(f"✅ Stored result → {analysis.name}: {result_value} ({stored_result.interpretation})")

                                except Exception as e:
                                    print(f"❌ Error in OBX parsing: {e}")
                            
                            messages_received += 1

                    except Exception as parse_error:
                        print(f"❌ Failed to parse HL7 message: {parse_error}")
                        print(f"Raw response:\n{response}")
                        messages_received += 1

            sock.shutdown(socket.SHUT_RDWR)

    except socket.timeout:
        print(f"⏱️ Timeout connecting to {device_name}")
    except ConnectionRefusedError:
        print(f"🚫 Connection refused by {device_name}")
    except Exception as e:
        print(f"💥 Error communicating with {device_name}: {e}")
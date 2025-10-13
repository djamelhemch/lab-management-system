from fastapi import APIRouter, Depends, BackgroundTasks, HTTPException
from sqlalchemy.orm import Session
import socket
from hl7apy.parser import parse_message

from app.schemas.lab_device import LabDeviceCreate, LabDevice, FHIRObservation
from app.crud.lab_device import get_device, get_devices, create_device, create_fhir_resource
from app.database import get_db

from app.crud.lab_result import create_lab_result
from app.schemas.lab_result import LabResultCreate
from app.models.patient import Patient
from app.models.analysis import NormalRange, AnalysisCatalog
from app.schemas.lab_result import LabResultCreate
from datetime import datetime
router = APIRouter(prefix="/lab-devices", tags=["Lab Devices"])

# MLLP Helper Functions
def mllp_wrap(message: str) -> bytes:
    """Wrap HL7 message with MLLP start and end block characters."""
    return b'\x0b' + message.encode('utf-8') + b'\x1c\r'

def mllp_unwrap(data: bytes) -> str:
    """Safely unwrap MLLP, preserving HL7 structure and carriage returns."""
    # MLLP framing characters
    START_BLOCK = b'\x0b'
    END_BLOCK = b'\x1c\r'

    # Only remove exact framing (not all control chars)
    if data.startswith(START_BLOCK):
        data = data[len(START_BLOCK):]
    if data.endswith(END_BLOCK):
        data = data[:-len(END_BLOCK)]

    # Decode normally
    return data.decode('utf-8', errors='ignore')

# Device registry endpoints
@router.post("/", response_model=LabDevice)
def create_lab_device(device: LabDeviceCreate, db: Session = Depends(get_db)):
    return create_device(db, device)

@router.get("/", response_model=list[LabDevice])
def list_lab_devices(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    return get_devices(db, skip, limit)

@router.get("/{device_id}", response_model=LabDevice)
def get_lab_device(device_id: int, db: Session = Depends(get_db)):
    device = get_device(db, device_id)
    if not device:
        raise HTTPException(status_code=404, detail="Device not found")
    return device

def tcp_client_send_receive_store(device_ip: str, device_port: int, message: str, device_name: str, db: Session):
    import datetime
    from app.models.quotation import Quotation, QuotationItem

    try:
        with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as sock:
            sock.settimeout(15)
            sock.connect((device_ip, device_port))

            sock.sendall(mllp_wrap(message))
            print(f"[{datetime.datetime.now()}] 📤 Sent HL7 message to {device_name} ({device_ip}:{device_port})")

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
                    print(f"[{datetime.datetime.now()}] 📥 Received HL7 response ({len(response)} bytes):\n{response}\n---")

                    try:
                        hl7_msg = parse_message(response)
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

        
@router.post("/devices/{device_id}/send_hl7_order/")
async def send_hl7_order_to_device(device_id: int, background_tasks: BackgroundTasks, db: Session = Depends(get_db)):
    device = get_device(db, device_id)
    if not device:
        raise HTTPException(status_code=404, detail="Device not found")

    # Properly formatted HL7 order message - ensure clean structure
    timestamp = datetime.now().strftime('%Y%m%d%H%M%S')
    hl7_order_message = (
        f"MSH|^~\\&|LIS|HOSPITAL|DEVICE|LAB|{timestamp}||ORM^O01|MSG{timestamp}|P|2.3.1\r"
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
    return {"status": f"HL7 order sent to device {device.name} in background"}


@router.post("/send_all_orders/")
async def send_hl7_order_to_all_devices(background_tasks: BackgroundTasks, db: Session = Depends(get_db)):
    devices = get_devices(db)
    if not devices:
        raise HTTPException(status_code=404, detail="No devices found")

    hl7_order_message = (
        "MSH|^~\\&|LIS|HOSPITAL|DEVICE|LAB|202509291000||ORM^O01|54321|P|2.3.1\r"
        "PID|||123456||DOE^JOHN\r"
        "ORC|NW|1001\r"
        "OBR|1|||GLU^Glucose Test\r"
    )

    for device in devices:
        background_tasks.add_task(
            tcp_client_send_receive_store,
            device.ip_address,
            device.tcp_port,
            hl7_order_message,
            device.name,
            db
        )
    
    return {"status": f"HL7 orders sent to {len(devices)} devices in background"}

@router.post("/{device_id}/test_connection/")
async def test_device_connection(device_id: int, db: Session = Depends(get_db)):
    device = get_device(db, device_id)
    if not device:
        raise HTTPException(status_code=404, detail="Device not found")
    
    try:
        with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as sock:
            sock.settimeout(5)
            sock.connect((device.ip_address, device.tcp_port))
            return {"status": f"Successfully connected to {device.name}"}
    except Exception as e:
        return {"status": f"Failed to connect to {device.name}: {str(e)}"}


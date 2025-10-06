from fastapi import APIRouter, Depends, BackgroundTasks, HTTPException
from sqlalchemy.orm import Session
import socket
from hl7apy.parser import parse_message

from app.schemas.lab_device import LabDeviceCreate, LabDevice, FHIRObservation
from app.crud.lab_device import get_device, get_devices, create_device, create_fhir_resource
from app.database import get_db

router = APIRouter(prefix="/lab-devices", tags=["Lab Devices"])

# MLLP Helper Functions
def mllp_wrap(message: str) -> bytes:
    """Wrap HL7 message with MLLP framing"""
    return b'\x0b' + message.encode() + b'\x1c\r'

def mllp_unwrap(data: bytes) -> str:
    """Unwrap HL7 message from MLLP framing"""
    if data.startswith(b'\x0b'):
        data = data[1:]
    if data.endswith(b'\x1c\r'):
        data = data[:-2]
    return data.decode()

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

# HL7 TCP communication with robust MLLP handling
def tcp_client_send_receive_store(device_ip: str, device_port: int, message: str, device_name: str, db: Session):
    try:
        with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as sock:
            sock.settimeout(15)  # Increased timeout
            sock.connect((device_ip, device_port))
            
            # Send HL7 message with MLLP framing
            sock.sendall(mllp_wrap(message))
            print(f"Sent HL7 message to {device_name} ({device_ip}:{device_port})")
            
            # Receive response with MLLP handling
            buffer = b""
            while True:
                data = sock.recv(4096)
                if not data:
                    break
                buffer += data
                
                # Check for complete MLLP message
                if b'\x1c\r' in buffer:
                    response = mllp_unwrap(buffer)
                    print(f"Received HL7 response from {device_name}: {response}")
                    
                    # Parse HL7 and convert to FHIR
                    try:
                        hl7_msg = parse_message(response)
                        
                        # Check if it's a result message (ORU^R01)
                        if hl7_msg.segment('MSH').message_type.value.startswith('ORU'):
                            obx = hl7_msg.segment('OBX')
                            observation = FHIRObservation(
                                status="final",
                                code={
                                    "coding": [{
                                        "system": "http://loinc.org",
                                        "code": obx.observation_identifier.identifier.value,
                                        "display": obx.observation_identifier.text.value
                                    }]
                                },
                                subject={"reference": f"Patient/{hl7_msg.segment('PID').patient_id.patient_id.value}"},
                                effectiveDateTime=hl7_msg.segment('OBR').observation_date_time.value,
                                valueQuantity={
                                    "value": float(obx.observation_value[0].value),
                                    "unit": obx.units.identifier.value,
                                    "system": "http://unitsofmeasure.org",
                                    "code": obx.units.identifier.value
                                }
                            )
                            
                            # Store FHIR resource
                            resource_dict = observation.dict(by_alias=True)
                            resource_dict["device_source"] = device_name  # Add device context
                            created_resource = create_fhir_resource(db, "Observation", resource_dict)
                            print(f"Created FHIR Observation resource with ID: {created_resource.id}")
                            
                    except Exception as parse_error:
                        print(f"Error parsing HL7 response from {device_name}: {parse_error}")
                    
                    break
            
            # Graceful socket shutdown
            sock.shutdown(socket.SHUT_RDWR)
            
    except socket.timeout:
        print(f"Timeout connecting to device {device_name} ({device_ip}:{device_port})")
    except ConnectionRefusedError:
        print(f"Connection refused by device {device_name} ({device_ip}:{device_port})")
    except Exception as e:
        print(f"Error communicating with device {device_name}: {e}")

@router.post("/devices/{device_id}/send_hl7_order/")
async def send_hl7_order_to_device(device_id: int, background_tasks: BackgroundTasks, db: Session = Depends(get_db)):
    device = get_device(db, device_id)
    if not device:
        raise HTTPException(status_code=404, detail="Device not found")

    # Properly formatted HL7 order message with correct segment separation
    hl7_order_message = (
        "MSH|^~\\&|LIS|HOSPITAL|DEVICE|LAB|202509291000||ORM^O01|54321|P|2.3.1\r"
        "PID|||123456||DOE^JOHN\r"
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

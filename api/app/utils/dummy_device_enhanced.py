# dummy_device_enhanced.py
import socket
import threading
import time
import random
from datetime import datetime
from typing import Dict, List, Optional


# ==================== Test Result Generators ====================

class TestGenerator:
    """Generate realistic test results with normal/abnormal values"""
    
    TESTS = {
        # Chemistry (Mindray CL-900i, BS-240)
        "GLU": {"name": "Glucose", "unit": "mmol/L", "range": (3.9, 5.5), "variance": 0.5},
        "ALT": {"name": "Alanine Aminotransferase", "unit": "U/L", "range": (7, 56), "variance": 10},
        "AST": {"name": "Aspartate Aminotransferase", "unit": "U/L", "range": (10, 40), "variance": 8},
        "CHOL": {"name": "Total Cholesterol", "unit": "mmol/L", "range": (3.0, 5.2), "variance": 0.5},
        "TRIG": {"name": "Triglycerides", "unit": "mmol/L", "range": (0.4, 1.7), "variance": 0.3},
        "CREA": {"name": "Creatinine", "unit": "µmol/L", "range": (62, 106), "variance": 10},
        "UREA": {"name": "Urea", "unit": "mmol/L", "range": (2.5, 7.5), "variance": 1.0},
        "cTnI": {"name": "Troponin I", "unit": "ng/mL", "range": (0.0, 0.04), "variance": 0.01},
        "TSH": {"name": "Thyroid Stimulating Hormone", "unit": "mIU/L", "range": (0.4, 4.0), "variance": 0.5},
        
        # Hematology (Mindray BC-30i)
        "WBC": {"name": "White Blood Cells", "unit": "10^9/L", "range": (4.0, 11.0), "variance": 1.5},
        "RBC": {"name": "Red Blood Cells", "unit": "10^12/L", "range": (4.2, 5.8), "variance": 0.3},
        "HGB": {"name": "Hemoglobin", "unit": "g/dL", "range": (13.0, 17.0), "variance": 1.0},
        "HCT": {"name": "Hematocrit", "unit": "%", "range": (40, 52), "variance": 3},
        "PLT": {"name": "Platelets", "unit": "10^9/L", "range": (150, 400), "variance": 50},
        
        # Electrolytes (i-sens i-smart 30pro)
        "NA": {"name": "Sodium", "unit": "mmol/L", "range": (136, 145), "variance": 2},
        "K": {"name": "Potassium", "unit": "mmol/L", "range": (3.5, 5.1), "variance": 0.3},
        "CL": {"name": "Chloride", "unit": "mmol/L", "range": (98, 107), "variance": 2},
        
        # HPLC (Tosoh HLC-723GX)
        "HbA1c": {"name": "Hemoglobin A1c", "unit": "%", "range": (4.0, 5.6), "variance": 0.5},
        
        # Coagulation (Thrombotimer)
        "PT": {"name": "Prothrombin Time", "unit": "sec", "range": (11.0, 13.5), "variance": 1.0},
        "INR": {"name": "INR", "unit": "", "range": (0.8, 1.2), "variance": 0.1},
        "APTT": {"name": "Activated Partial Thromboplastin Time", "unit": "sec", "range": (25, 35), "variance": 3},
    }
    
    @classmethod
    def generate_result(cls, test_code: str, abnormal_chance: float = 0.2) -> tuple:
        """Generate a test result value and interpretation"""
        if test_code not in cls.TESTS:
            return (None, None, None)
        
        test = cls.TESTS[test_code]
        min_val, max_val = test["range"]
        variance = test["variance"]
        
        # 20% chance of abnormal result
        if random.random() < abnormal_chance:
            # Generate abnormal value
            if random.random() < 0.5:
                # Below range
                value = min_val - random.uniform(0, variance)
                interpretation = "L"
            else:
                # Above range
                value = max_val + random.uniform(0, variance)
                interpretation = "H"
        else:
            # Normal value
            value = random.uniform(min_val, max_val)
            interpretation = "N"
        
        # Round to appropriate decimals
        if test["unit"] in ["10^9/L", "10^12/L", "%"]:
            value = round(value, 1)
        else:
            value = round(value, 2)
        
        reference_range = f"{min_val}-{max_val}"
        
        return (value, interpretation, reference_range)


# ==================== Device Profiles ====================

DEVICE_PROFILES = {
    "CL-900i": {
        "manufacturer": "Mindray",
        "model": "CL-900i",
        "port": 2575,
        "test_menu": ["cTnI", "TSH", "GLU", "CHOL"],
        "hl7_version": "2.3.1",
        "processing_time": 2.0,  # seconds
    },
    "BS-240": {
        "manufacturer": "Mindray",
        "model": "BS-240",
        "port": 2576,
        "test_menu": ["GLU", "ALT", "AST", "CHOL", "TRIG", "CREA", "UREA"],
        "hl7_version": "2.3.1",
        "processing_time": 1.5,
    },
    "BC-30i": {
        "manufacturer": "Mindray",
        "model": "BC-30i",
        "port": 2577,
        "test_menu": ["WBC", "RBC", "HGB", "HCT", "PLT"],
        "hl7_version": "2.3",
        "processing_time": 1.0,
    },
    "i-smart-30pro": {
        "manufacturer": "i-sens",
        "model": "i-smart 30pro",
        "port": 3030,
        "test_menu": ["NA", "K", "CL"],
        "hl7_version": "2.3.1",
        "processing_time": 0.5,
    },
    "HLC-723GX": {
        "manufacturer": "Tosoh",
        "model": "HLC-723GX",
        "port": 2578,
        "test_menu": ["HbA1c"],
        "hl7_version": "2.3.1",
        "processing_time": 2.2,
    },
    "Thrombotimer": {
        "manufacturer": "BEHNK",
        "model": "Thrombotimer 2-channel",
        "port": 4001,
        "test_menu": ["PT", "INR", "APTT"],
        "hl7_version": "2.3.1",
        "processing_time": 3.0,
    },
}


# ==================== MLLP Functions ====================

def mllp_wrap(msg: str) -> bytes:
    """Wrap HL7 message in MLLP framing"""
    return b'\x0b' + msg.encode("utf-8") + b'\x1c\r'


def mllp_unwrap(data: bytes) -> str:
    """Remove MLLP framing"""
    if data.startswith(b'\x0b'):
        data = data[1:]
    if data.endswith(b'\x1c\r'):
        data = data[:-2]
    return data.decode("utf-8")


# ==================== HL7 Message Parser ====================
def ensure_msh_header(msg: str) -> str:
    """Ensure HL7 message starts with MSH, otherwise wrap it."""
    if not msg.startswith("MSH|"):
        timestamp = datetime.now().strftime("%Y%m%d%H%M%S")
        msh_header = (
            f"MSH|^~\\&|DEVICE|LAB|LIS|HOSPITAL|{timestamp}||ACK^O01|AUTO{timestamp}|P|2.3.1\r"
        )
        msg = msh_header + msg
    return msg
    
def parse_hl7_order(message: str) -> Dict:
    """Safely parse HL7 message (ORM, ORU, or ACK) to extract patient/test info"""
    data = {
        "patient_id": None,
        "patient_name": None,
        "tests": [],
        "msg_control_id": None,
        "message_type": None,
    }

    if not message or not isinstance(message, str):
        return data

    lines = [line for line in message.split('\r') if line.strip()]
    for line in lines:
        parts = line.split('|')
        seg = parts[0]

        # --- MSH segment (always identify message type and control ID)
        if seg == "MSH":
            if len(parts) > 8:
                data["message_type"] = parts[8]
            if len(parts) > 9:
                data["msg_control_id"] = parts[9]

        # --- PID segment (patient details)
        elif seg == "PID":
            if len(parts) > 3:
                data["patient_id"] = parts[3]
            if len(parts) > 5:
                data["patient_name"] = parts[5]

        # --- OBR segment (test order)
        elif seg == "OBR":
            if len(parts) > 4:
                test_field = parts[4]
                if '^' in test_field:
                    test_code = test_field.split('^')[0]
                    data["tests"].append(test_code)
                else:
                    data["tests"].append(test_field)

        # --- OBX segment (result data, if present)
        elif seg == "OBX":
            if len(parts) > 3:
                test_field = parts[3]
                if '^' in test_field:
                    test_code = test_field.split('^')[0]
                    if test_code not in data["tests"]:
                        data["tests"].append(test_code)

    # If no MSH found, inject a dummy control ID
    if not data["msg_control_id"]:
        data["msg_control_id"] = "UNKNOWN"

    return data

# ==================== Device Handler ====================
def handle_client(conn, addr, device_profile: Dict):
    """Handle incoming connection from LIS"""
    device_name = f"{device_profile['manufacturer']} {device_profile['model']}"
    print(f"\n{'='*60}")
    print(f"📡 [{device_name}] Client connected from {addr}")
    print(f"{'='*60}")
    
    buffer = b""
    
    try:
        while True:
            data = conn.recv(4096)
            if not data:
                break
            buffer += data

            if b'\x1c\r' in buffer:
                msg = mllp_unwrap(buffer)
                msg = ensure_msh_header(msg)
                print(f"\n📥 [{device_name}] Received HL7 message:")
                print(f"{'─'*60}")
                print(msg)
                print(f"{'─'*60}")

                # Parse the order
                order_data = parse_hl7_order(msg)
                print(f"\n🔍 Parsed Order:")
                print(f"   Patient ID: {order_data['patient_id']}")
                print(f"   Patient Name: {order_data['patient_name']}")
                print(f"   Tests Requested: {', '.join(order_data['tests']) or 'None'}")

                timestamp = datetime.now().strftime('%Y%m%d%H%M%S')
                msg_control_id = order_data['msg_control_id'] or "MSG00000"

                # ✅ STEP 1: Send proper ACK message
                # CRITICAL: MSH must be the FIRST segment
                ack_msg = '\r'.join([
                    f"MSH|^~\\&|{device_profile['model']}|LAB|LIS|HOSPITAL|{timestamp}||ACK^O01|ACK{timestamp}|P|{device_profile['hl7_version']}",
                    f"MSA|AA|{msg_control_id}"
                ]) + '\r'
                conn.sendall(mllp_wrap(ack_msg))
                print(f"\n✅ [{device_name}] Sent ACK")
                print(f"ACK Message:\n{ack_msg}\n")

                # Simulate processing time
                print(f"⏳ [{device_name}] Analyzing sample... ({device_profile['processing_time']}s)")
                time.sleep(device_profile['processing_time'])

                # Determine which tests to send results for
                tests_to_report = order_data['tests'] if order_data['tests'] else device_profile['test_menu']
                
                # Filter to only tests this device can perform
                available_tests = [t for t in tests_to_report if t in device_profile['test_menu']]
                
                if not available_tests:
                    print(f"⚠️ [{device_name}] No matching tests in our menu, using default test panel")
                    available_tests = device_profile['test_menu'][:3]

                # ✅ STEP 2: Build proper ORU^R01 message
                timestamp = datetime.now().strftime('%Y%m%d%H%M%S')
                
                # Build all segments in correct order
                segments = []
                
                # Segment 1: MSH (MUST be first!)
                segments.append(
                    f"MSH|^~\\&|{device_profile['model']}|LAB|LIS|HOSPITAL|{timestamp}||ORU^R01|RSLT{timestamp}|P|{device_profile['hl7_version']}"
                )
                
                # Segment 2: PID
                segments.append(
                    f"PID|1||{order_data['patient_id'] or 'UNKNOWN'}||{order_data['patient_name'] or 'TEST^PATIENT'}||19900101|M"
                )
                
                # Segment 3: OBR
                segments.append(
                    f"OBR|1|||PANEL^{device_profile['model']} Panel"
                )
                
                # Segments 4+: OBX for each test result
                print(f"\n🧪 [{device_name}] Generated Results:")
                obx_counter = 1
                for test_code in available_tests:
                    value, interp, ref_range = TestGenerator.generate_result(test_code)
                    if value is not None:
                        test_info = TestGenerator.TESTS[test_code]
                        
                        segments.append(
                            f"OBX|{obx_counter}|NM|{test_code}^{test_info['name']}||{value}|{test_info['unit']}|{ref_range}|{interp}|||F"
                        )
                        
                        flag = "🔴" if interp == "H" else "🔵" if interp == "L" else "🟢"
                        print(f"   {flag} {test_code}: {value} {test_info['unit']} ({interp}) [{ref_range}]")
                        
                        obx_counter += 1
                
                # Join all segments with \r
                oru_msg = '\r'.join(segments) + '\r' 
                
                print(f"\n📤 [{device_name}] Sending ORU^R01:")
                print(f"ORU Message:\n{oru_msg}\n")

                conn.sendall(mllp_wrap(oru_msg))
                print(f"✅ [{device_name}] Sent ORU^R01 result message")
                print(f"{'='*60}\n")

                buffer = b""

    except ConnectionResetError:
        print(f"⚠️ [{device_name}] Connection reset by {addr}")
    except Exception as e:
        print(f"💥 [{device_name}] Error: {e}")
        import traceback
        traceback.print_exc()
    finally:
        conn.close()
        print(f"🔌 [{device_name}] Client disconnected\n")


# ==================== Multi-Device Server ====================

def start_device_server(device_name: str, device_profile: Dict):
    """Start a server for one device"""
    HOST = "127.0.0.1"
    PORT = device_profile['port']
    
    server = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    
    try:
        server.bind((HOST, PORT))
        server.listen(5)
        print(f"✅ {device_profile['manufacturer']} {device_profile['model']} listening on {HOST}:{PORT}")
    except OSError as e:
        print(f"❌ Failed to start {device_name} on port {PORT}: {e}")
        return

    while True:
        try:
            conn, addr = server.accept()
            threading.Thread(
                target=handle_client, 
                args=(conn, addr, device_profile), 
                daemon=True
            ).start()
        except Exception as e:
            print(f"Error accepting connection for {device_name}: {e}")


def main():
    """Start all device simulators"""
    print("\n" + "="*60)
    print("🧪 Multi-Device Laboratory Analyzer Simulator")
    print("="*60)
    print("\nStarting device servers...\n")
    
    # Start a thread for each device
    threads = []
    for device_name, device_profile in DEVICE_PROFILES.items():
        thread = threading.Thread(
            target=start_device_server,
            args=(device_name, device_profile),
            daemon=True
        )
        thread.start()
        threads.append(thread)
        time.sleep(0.1)  # Stagger startup
    
    print("\n" + "="*60)
    print("✅ All devices ready and listening!")
    print("="*60)
    print("\nDevice Test Menus:")
    for device_name, profile in DEVICE_PROFILES.items():
        print(f"\n📱 {profile['manufacturer']} {profile['model']} (Port {profile['port']}):")
        print(f"   Tests: {', '.join(profile['test_menu'])}")
    
    print("\n" + "="*60)
    print("Press Ctrl+C to stop all devices")
    print("="*60 + "\n")
    
    try:
        # Keep main thread alive
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        print("\n\n🛑 Shutting down all devices...")


if __name__ == "__main__":
    main()
# End of dummy_device_enhanced.py
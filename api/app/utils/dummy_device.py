import socket
import threading
import time
from datetime import datetime

def mllp_wrap(msg: str) -> bytes:
    """Wrap HL7 message in MLLP (start/end control characters)."""
    return b'\x0b' + msg.encode("utf-8") + b'\x1c\r'

def mllp_unwrap(data: bytes) -> str:
    """Remove MLLP framing from received data."""
    if data.startswith(b'\x0b'):
        data = data[1:]
    if data.endswith(b'\x1c\r'):
        data = data[:-2]
    return data.decode("utf-8")

def handle_client(conn, addr):
    print(f"📡 Client connected from {addr}")
    buffer = b""
    try:
        while True:
            data = conn.recv(4096)
            if not data:
                break
            buffer += data

            if b'\x1c\r' in buffer:
                msg = mllp_unwrap(buffer)
                print("📥 Received HL7 message:\n", msg)

                timestamp = datetime.now().strftime('%Y%m%d%H%M%S')
                
                # Extract message control ID from received MSH if possible
                msg_control_id = "MSG54321"
                try:
                    if msg.startswith("MSH"):
                        parts = msg.split('|')
                        if len(parts) > 9:
                            msg_control_id = parts[9]
                except:
                    pass

                # ✅ Send proper ACK message - MSH segment MUST be first
                ack_msg = (
                    f"MSH|^~\\&|DEVICE|LAB|LIS|HOSPITAL|{timestamp}||ACK^O01|ACK{timestamp}|P|2.3.1\r"
                    f"MSA|AA|{msg_control_id}"
                )
                conn.sendall(mllp_wrap(ack_msg))
                print("📤 Sent valid ACK ✅")

                # Wait to simulate analyzer processing
                time.sleep(1)

                # ✅ Send valid ORU^R01 result message - MSH MUST be first
                result_value = round(3.9 + (1.6 * (datetime.now().second % 10) / 10), 1)
                oru_msg = (
                    f"MSH|^~\\&|DEVICE|LAB|LIS|HOSPITAL|{timestamp}||ORU^R01|RSLT{timestamp}|P|2.3.1\r"
                    "PID|1||P2025014||TEST^PATIENT||19900101|M\r"
                    "OBR|1|||GLU^Glucose Test\r"
                    f"OBX|1|NM|GLU^Glucose||{result_value}|mmol/L|3.9-5.5|N|||F"
                )
                conn.sendall(mllp_wrap(oru_msg))
                print(f"📤 Sent ORU^R01 result → GLU = {result_value} mmol/L ✅")

                buffer = b""

    except ConnectionResetError:
        print(f"⚠️ Connection reset by {addr}")
    except Exception as e:
        print(f"💥 Error handling client {addr}: {e}")
    finally:
        conn.close()
        print(f"🔌 Client disconnected: {addr}\n")

def start_server():
    HOST = "127.0.0.1"
    PORT = 2575
    server = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    server.bind((HOST, PORT))
    server.listen(5)
    print(f"🧪 Dummy device listening on {HOST}:{PORT} ...")

    while True:
        conn, addr = server.accept()
        threading.Thread(target=handle_client, args=(conn, addr), daemon=True).start()

if __name__ == "__main__":
    start_server()

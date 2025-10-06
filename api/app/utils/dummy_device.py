# dummy_device.py - Updated version
import socket
import threading

HL7_MESSAGE = (
    "MSH|^~\\&|LAB|DEVICE|LIS|HOSPITAL|202509291000||ORU^R01|12345|P|2.3.1\r"
    "PID|||123456||DOE^JOHN\r"
    "OBR|1|||TEST^Blood Test\r"
    "OBX|1|NM|GLU^Glucose||5.4|mmol/L|3.9-5.5|N|||F\r"
)

def mllp_wrap(message: str) -> bytes:
    return b'\x0b' + message.encode() + b'\x1c\r'

def mllp_unwrap(data: bytes) -> str:
    if data.startswith(b'\x0b'):
        data = data[1:]
    if data.endswith(b'\x1c\r'):
        data = data[:-2]
    return data.decode()

def handle_client(client_socket):
    print("Client connected")
    try:
        # Send initial HL7 result message
        client_socket.sendall(mllp_wrap(HL7_MESSAGE))
        
        buffer = b""
        while True:
            data = client_socket.recv(1024)
            if not data:
                print("Client closed connection")
                break
            buffer += data
            
            # Process complete MLLP messages
            while b'\x1c\r' in buffer:
                split_idx = buffer.find(b'\x1c\r') + 2
                hl7_data = mllp_unwrap(buffer[:split_idx])
                print("Received HL7 message:", hl7_data)
                
                # Send ACK response
                ack_message = (
                    "MSH|^~\\&|DEVICE|LAB|HOSPITAL|LIS|202509291001||ACK^R01|54321|P|2.3.1\r"
                    "MSA|AA|12345\r"
                )
                client_socket.sendall(mllp_wrap(ack_message))
                buffer = buffer[split_idx:]
                
    except ConnectionResetError:
        print("Connection reset by client")
    finally:
        client_socket.close()
        print("Client disconnected")

def start_server():
    server = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    server.bind(("0.0.0.0", 2575))
    server.listen(5)
    print("Dummy device server listening on port 2575...")
    
    while True:
        client_sock, addr = server.accept()
        client_thread = threading.Thread(target=handle_client, args=(client_sock,))
        client_thread.start()

if __name__ == "__main__":
    start_server()

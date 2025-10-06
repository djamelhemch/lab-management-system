import socket

def mllp_wrap(message: str) -> bytes:
    return b'\x0b' + message.encode() + b'\x1c\r'

def mllp_unwrap(data: bytes) -> str:
    if data.startswith(b'\x0b'):
        data = data[1:]
    if data.endswith(b'\x1c\r'):
        data = data[:-2]
    return data.decode()

def tcp_client_send_receive(device_ip: str, device_port: int, hl7_message: str):
    with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as sock:
        sock.settimeout(10)
        sock.connect((device_ip, device_port))
        sock.sendall(mllp_wrap(hl7_message))
        # Receive HL7 response (MLLP framed)
        buffer = b""
        while True:
            data = sock.recv(1024)
            if not data:
                break
            buffer += data
            if b'\x1c\r' in buffer:
                reply = mllp_unwrap(buffer)
                print("Received HL7 reply:", reply)
                break
        sock.shutdown(socket.SHUT_RDWR)
        sock.close()
        return reply

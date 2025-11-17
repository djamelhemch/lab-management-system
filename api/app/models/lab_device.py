import sqlalchemy
from sqlalchemy import Column, BigInteger, String, Integer, Boolean, DateTime, Text, JSON, ForeignKey
from sqlalchemy.orm import relationship
from app.database import Base  # Your SQLAlchemy Base import

class LabDevice(Base):
    __tablename__ = "lab_devices"

    id = Column(Integer, primary_key=True, autoincrement=True)
    name = Column(String(100), nullable=False)
    manufacturer = Column(String(100), nullable=True, index=True)
    model = Column(String(100), nullable=True)
    serial_number = Column(String(100), unique=True, nullable=True)
    ip_address = Column(String(45), nullable=True)
    tcp_port = Column(Integer, nullable=True)
    connection_type = Column(String(30), nullable=False, default='tcp_client')
    is_device_initiates = Column(Boolean, default=True)
    protocol_type = Column(String(20), nullable=False, default='HL7')
    device_type = Column(String(50), nullable=True)
    hl7_version = Column(String(20), default='2.3.1')
    encoding = Column(String(20), default='utf-8')
    interface_mode = Column(String(20), nullable=False, default='bidirectional')
    supports_orders = Column(Boolean, default=False)
    supports_queries = Column(Boolean, default=False)
    supports_qc = Column(Boolean, default=False)
    connection_mode = Column(String(20), nullable=False, default='per_message')
    expects_ack_for_results = Column(Boolean, default=True)
    max_messages_per_connection = Column(Integer, nullable=True)
    connection_timeout = Column(Integer, default=10)
    read_timeout = Column(Integer, default=30)
    retry_attempts = Column(Integer, default=3)
    retry_delay = Column(Integer, default=5)
    is_active = Column(Boolean, default=True)
    status = Column(String(20), nullable=False, default='offline', index=True)
    last_connected = Column(DateTime, nullable=True)
    last_error = Column(Text, nullable=True)
    last_heartbeat = Column(DateTime, nullable=True)
    description = Column(Text, nullable=True)
    custom_config = Column(JSON, nullable=True)
    created_at = Column(DateTime, server_default=sqlalchemy.func.current_timestamp())
    updated_at = Column(DateTime, server_default=sqlalchemy.func.current_timestamp(), 
                       onupdate=sqlalchemy.func.current_timestamp())
    
    # Relationships
    messages = relationship("DeviceMessage", back_populates="device", cascade="all, delete-orphan")
    fhir_resources = relationship("FhirResource", back_populates="device")

    def __repr__(self):
        return f"<LabDevice(id={self.id}, name='{self.name}', model='{self.model}', status='{self.status}')>"


class DeviceMessage(Base):
    __tablename__ = "device_messages"
    
    id = Column(Integer, primary_key=True, autoincrement=True)
    device_id = Column(Integer, ForeignKey('lab_devices.id', ondelete='CASCADE'), nullable=False, index=True)
    message_control_id = Column(String(100), unique=True, nullable=False, index=True)
    message_type = Column(String(20), nullable=True, index=True)
    direction = Column(String(10), nullable=True)
    raw_message = Column(Text, nullable=True)
    parsed_data = Column(JSON, nullable=True)
    status = Column(String(20), default='pending', index=True)
    ack_received = Column(Boolean, default=False)
    ack_code = Column(String(5), nullable=True)
    error_message = Column(Text, nullable=True)
    created_at = Column(DateTime, server_default=sqlalchemy.func.current_timestamp(), index=True)
    sent_at = Column(DateTime, nullable=True)
    acknowledged_at = Column(DateTime, nullable=True)
    processed_at = Column(DateTime, nullable=True)
    
    # Relationships
    device = relationship("LabDevice", back_populates="messages")
    fhir_resources = relationship("FhirResource", back_populates="source_message")

    def __repr__(self):
        return f"<DeviceMessage(id={self.id}, type='{self.message_type}', status='{self.status}')>"


class FhirResource(Base):
    __tablename__ = "fhir_resources"

    id = Column(Integer, primary_key=True, autoincrement=True)
    resource_type = Column(String(50), nullable=True, index=True)
    resource_json = Column(JSON, nullable=True)
    device_source = Column(String(100), nullable=True)
    device_id = Column(Integer, ForeignKey('lab_devices.id', ondelete='SET NULL'), nullable=True, index=True)
    source_message_id = Column(Integer, ForeignKey('device_messages.id', ondelete='SET NULL'), nullable=True, index=True)
    created_at = Column(DateTime, server_default=sqlalchemy.func.current_timestamp())
    
    # Relationships
    device = relationship("LabDevice", back_populates="fhir_resources")
    source_message = relationship("DeviceMessage", back_populates="fhir_resources")

    def __repr__(self):
        return f"<FhirResource(id={self.id}, type='{self.resource_type}')>"
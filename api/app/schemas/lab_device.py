from pydantic import BaseModel, Field
from typing import Optional, Dict, Any, Literal, List, Union
from datetime import datetime
class LabDeviceBase(BaseModel):
    name: str
    manufacturer: Optional[str] = None
    model: Optional[str] = None
    serial_number: Optional[str] = None
    description: Optional[str] = None


class LabDeviceCreate(LabDeviceBase):
    connection_type: Literal['tcp_client', 'tcp_server', 'serial', 'serial_via_converter', 'middleware'] = 'tcp_client'
    ip_address: Optional[str] = None
    tcp_port: Optional[int] = 2575
    is_device_initiates: bool = True
    protocol_type: Literal['HL7', 'ASTM', 'custom'] = 'HL7'
    hl7_version: Optional[str] = "2.3.1"
    encoding: str = "utf-8"
    interface_mode: Literal['unsolicited', 'query', 'bidirectional'] = 'bidirectional'
    supports_orders: bool = False
    supports_queries: bool = False
    supports_qc: bool = False
    connection_mode: Literal['persistent', 'per_message'] = 'per_message'
    expects_ack_for_results: bool = True
    max_messages_per_connection: Optional[int] = None
    connection_timeout: int = 10
    read_timeout: int = 30
    retry_attempts: int = 3
    retry_delay: int = 5
    device_type: Optional[str] = None
    custom_config: Dict[str, Any] = {}


class LabDeviceUpdate(BaseModel):
    name: Optional[str] = None
    manufacturer: Optional[str] = None
    model: Optional[str] = None
    ip_address: Optional[str] = None
    tcp_port: Optional[int] = None
    connection_timeout: Optional[int] = None
    read_timeout: Optional[int] = None
    is_active: Optional[bool] = None
    status: Optional[Literal['online', 'offline', 'error', 'testing']] = None
    description: Optional[str] = None
    custom_config: Optional[Dict[str, Any]] = None


class LabDevice(LabDeviceBase):
    id: int
    connection_type: str
    ip_address: Optional[str]
    tcp_port: Optional[int]
    is_device_initiates: bool
    protocol_type: str
    hl7_version: Optional[str]
    encoding: Optional[str]
    interface_mode: str
    supports_orders: bool
    supports_queries: bool
    supports_qc: bool
    connection_mode: str
    expects_ack_for_results: bool
    max_messages_per_connection: Optional[int]
    connection_timeout: Optional[int]
    read_timeout: Optional[int]
    retry_attempts: Optional[int]
    retry_delay: Optional[int]
    is_active: bool
    status: str
    last_connected: Optional[datetime]
    last_error: Optional[str]
    last_heartbeat: Optional[datetime]
    device_type: Optional[str]
    custom_config: Optional[Dict[str, Any]]
    created_at: datetime
    updated_at: datetime
    
    class Config:
        from_attributes = True


class DeviceMessageCreate(BaseModel):
    device_id: int
    message_control_id: str
    message_type: str
    direction: Literal['inbound', 'outbound']
    raw_message: str
    parsed_data: Optional[Dict[str, Any]] = None


class DeviceMessage(DeviceMessageCreate):
    id: int
    status: str
    ack_received: bool
    ack_code: Optional[str]
    error_message: Optional[str]
    created_at: datetime
    sent_at: Optional[datetime]
    acknowledged_at: Optional[datetime]
    processed_at: Optional[datetime]
    
    class Config:
        from_attributes = True


class DeviceStats(BaseModel):
    total_messages: int
    messages_today: int
    pending_messages: int
    failed_messages: int
    last_message_time: Optional[datetime]


class FhirResourceCreate(BaseModel):
    resource_type: str
    resource_json: Dict[str, Any]
    device_source: Optional[str] = None
    device_id: Optional[int] = None
    source_message_id: Optional[int] = None


class FhirResource(FhirResourceCreate):
    id: int
    created_at: datetime
    
    class Config:
        from_attributes = True
from pydantic import BaseModel, Field
from typing import Optional, Dict, Any

class LabDeviceBase(BaseModel):
    name: str
    ip_address: str
    tcp_port: int
    device_type: Optional[str] = None
    hl7_version: Optional[str] = "2.3.1"
    description: Optional[str] = None

class LabDeviceCreate(LabDeviceBase):
    pass

class LabDevice(LabDeviceBase):
    id: int

    class Config:
        orm_mode = True

class FHIRObservation(BaseModel):
    resourceType: str = "Observation"
    status: str
    code: Dict[str, Any]
    subject: Dict[str, Any]
    effectiveDateTime: str
    valueQuantity: Dict[str, Any]
    id: Optional[int] = Field(None, alias="id")

    class Config:
        orm_mode = True

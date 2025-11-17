from sqlalchemy.orm import Session
from app.models.lab_device import LabDevice, FhirResource
from typing import Dict
from typing import List, Optional
# Lab Device CRUD
def get_device(db: Session, device_id: int) -> Optional[LabDevice]:
    """Fetch single device by ID"""
    return db.query(LabDevice).filter(LabDevice.id == device_id).first()

def get_devices(db: Session, skip: int = 0, limit: int = 100) -> List[LabDevice]:
    """Fetch multiple devices with pagination"""
    return db.query(LabDevice).offset(skip).limit(limit).all()

def create_device(db: Session, device) -> LabDevice:
    """Create new device from Pydantic model"""
    db_device = LabDevice(**device.dict())
    db.add(db_device)
    db.commit()
    db.refresh(db_device)
    return db_device

def update_device(db: Session, device_id: int, device_update) -> Optional[LabDevice]:
    """Update existing device by ID"""
    db_device = db.query(LabDevice).filter(LabDevice.id == device_id).first()
    if not db_device:
        return None
    
    update_data = device_update.dict(exclude_unset=True)
    for field, value in update_data.items():
        setattr(db_device, field, value)
    
    db.commit()
    db.refresh(db_device)
    return db_device

def delete_device(db: Session, device_id: int) -> bool:
    """Delete device by ID. Returns True if deleted, else False."""
    db_device = db.query(LabDevice).filter(LabDevice.id == device_id).first()
    if not db_device:
        return False
    db.delete(db_device)
    db.commit()
    return True

# FHIR Resource CRUD

def create_fhir_resource(db: Session, resource_type: str, resource_json: Dict) -> FhirResource:
    """Create a new FHIR resource"""
    db_resource = FhirResource(resource_type=resource_type, resource_json=resource_json)
    db.add(db_resource)
    db.commit()
    db.refresh(db_resource)
    return db_resource
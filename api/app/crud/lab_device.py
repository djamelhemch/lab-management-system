from sqlalchemy.orm import Session
from app.models.lab_device import LabDevice, FhirResource
from typing import Dict

# Lab Device CRUD
def get_device(db: Session, device_id: int):
    return db.query(LabDevice).filter(LabDevice.id == device_id).first()

def get_devices(db: Session, skip: int = 0, limit: int = 100):
    return db.query(LabDevice).offset(skip).limit(limit).all()

def create_device(db: Session, device):
    db_device = LabDevice(**device.dict())
    db.add(db_device)
    db.commit()
    db.refresh(db_device)
    return db_device

# FHIR Resource CRUD
def create_fhir_resource(db: Session, resource_type: str, resource_json: Dict):
    db_resource = FhirResource(resource_type=resource_type, resource_json=resource_json)
    db.add(db_resource)
    db.commit()
    db.refresh(db_resource)
    return db_resource

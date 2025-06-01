from sqlalchemy.orm import Session
from app.models.patient import Patient
from app.schemas.patient import PatientCreate, PatientUpdate

def create_patient(db: Session, patient: PatientCreate):
    db_patient = Patient(**patient.dict())
    db.add(db_patient)
    db.commit()
    db.refresh(db_patient)
    return db_patient

def get_patients(db: Session, skip: int = 0, limit: int = 100):
    return db.query(Patient).offset(skip).limit(limit).all()

def get_patient(db: Session, patient_id: int):
    return db.query(Patient).filter(Patient.id == patient_id).first()

def update_patient(db: Session, db_patient: Patient, patient_update: PatientUpdate):  
    update_data = patient_update.dict(exclude_unset=True)  
    for key, value in update_data.items():  
        setattr(db_patient, key, value)  
    db.commit()  
    db.refresh(db_patient)  
    return db_patient
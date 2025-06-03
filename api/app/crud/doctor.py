from sqlalchemy.orm import Session
from sqlalchemy import func
from app.database import get_db  # Correct import for database session
 
from app.models.doctor import Doctor  # Correct path to ORM model
from app.schemas.doctor import DoctorCreate,DoctorUpdate  # Correct import
from app.models.patient import Patient

def create_doctor(db: Session, doctor: DoctorCreate):
    db_doctor = Doctor(**doctor.dict())
    db.add(db_doctor)
    db.commit()
    db.refresh(db_doctor)
    return db_doctor

def get_doctors(db: Session, skip: int = 0, limit: int = 100):
    return db.query(Doctor).offset(skip).limit(limit).all()

def get_doctor(db: Session, doctor_id: int):
    return db.query(Doctor).filter(Doctor.id == doctor_id).first()

def count_doctors(db: Session):
    return db.query(Doctor).count()

def get_doctors_with_patient_count(db: Session, skip: int = 0, limit: int = 100):  
    # Join Doctor and Patient, count patients per doctor  
    results = (  
        db.query(  
            Doctor,  
            func.count(Patient.id).label("patient_count")  
        )  
        .outerjoin(Patient, Patient.doctor_id == Doctor.id)  
        .group_by(Doctor.id)  
        .offset(skip)  
        .limit(limit)  
        .all()  
    )  
    # Return as list of dicts  
    return [  
        {  
            **doctor.__dict__,  
            "patient_count": patient_count  
        }  
        for doctor, patient_count in results  
    ]

def update_doctor(db: Session, doctor_id: int, doctor_update: DoctorUpdate):  
    db_doctor = db.query(Doctor).filter(Doctor.id == doctor_id).first()  
    if not db_doctor:  
        return None  
    for field, value in doctor_update.dict(exclude_unset=True).items():  
        setattr(db_doctor, field, value)  
    db.commit()  
    db.refresh(db_doctor)  
    return db_doctor
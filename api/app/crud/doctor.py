from sqlalchemy.orm import Session
from sqlalchemy import func
from app.database import get_db  # Correct import for database session
 
from app.models.doctor import Doctor  # Correct path to ORM model
from app.schemas.doctor import DoctorCreate,DoctorUpdate  # Correct import
from app.models.patient import Patient
from sqlalchemy.exc import IntegrityError
from fastapi import HTTPException, status
def create_doctor(db: Session, doctor: DoctorCreate):
    """
    Create a new doctor with detailed conflict reporting.
    """
    
    conflicts = []
    
    # Check email
    if doctor.email:
        existing_by_email = db.query(Doctor).filter(
            Doctor.email == doctor.email
        ).first()
        if existing_by_email:
            conflicts.append(f"l'email '{doctor.email}'")
    
    # Check phone
    if doctor.phone:
        existing_by_phone = db.query(Doctor).filter(
            Doctor.phone == doctor.phone
        ).first()
        if existing_by_phone:
            conflicts.append(f"le téléphone '{doctor.phone}'")
    
    # Check name
    existing_by_name = db.query(Doctor).filter(
        Doctor.full_name == doctor.full_name
    ).first()
    if existing_by_name:
        conflicts.append(f"le nom '{doctor.full_name}'")
    
    # Handle conflicts
    if conflicts:
        if len(conflicts) == 1:
            detail = f"Un médecin avec {conflicts[0]} existe déjà."
        else:
            # Multiple conflicts: "Un médecin avec l'email '...' et le téléphone '...' existe déjà."
            fields_text = " et ".join(conflicts)
            detail = f"Un médecin avec {fields_text} existe déjà."
        
        raise HTTPException(
            status_code=status.HTTP_409_CONFLICT,
            detail=detail
        )
    
    # Create doctor
    try:
        db_doctor = Doctor(**doctor.dict())
        db.add(db_doctor)
        db.commit()
        db.refresh(db_doctor)
        return db_doctor
    except IntegrityError as e:
        db.rollback()
        raise HTTPException(
            status_code=status.HTTP_409_CONFLICT,
            detail="Un médecin avec ces coordonnées existe déjà."
        )

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
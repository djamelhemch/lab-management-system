from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session,joinedload
from sqlalchemy import func, or_  
from typing import List 

from app.database import SessionLocal, get_db
from app.schemas.patient import PatientCreate, PatientRead, PatientUpdate
from app.crud import patient as crud
from app.models.patient import Patient  # Import your ORM Patient model


router = APIRouter(prefix="/patients", tags=["Patients"])



@router.post("/", response_model=PatientRead)
def create_patient_route(patient: PatientCreate, db: Session = Depends(get_db)):
    return crud.create_patient(db, patient)

@router.get("/", response_model=List[PatientRead])  
def list_patients_route(  
    skip: int = 0,  
    limit: int = 100,  
    q: str = None,  
    db: Session = Depends(get_db)  
):  
    query = db.query(Patient).options(joinedload(Patient.doctor))  
    if q:  
        q_like = f"%{q}%"  
        query = query.filter(  
            or_(  
                Patient.first_name.ilike(q_like),  
                Patient.last_name.ilike(q_like),  
                Patient.file_number.ilike(q_like),  
                Patient.phone.ilike(q_like)  
            )  
        )  
    patients = query.order_by(Patient.created_at.desc()).offset(skip).limit(limit).all()  
    result = []  
    for p in patients:  
        data = p.__dict__.copy()  
        # Add doctor_name using the relationship  
        data['doctor_name'] = p.doctor.full_name if p.doctor else None  
        result.append(data)  
    return result

@router.get("/{patient_id}", response_model=PatientRead)
def get_patient_route(patient_id: int, db: Session = Depends(get_db)):
    db_patient = crud.get_patient(db, patient_id)
    if not db_patient:
        raise HTTPException(status_code=404, detail="Patient not found")
    return db_patient

@router.put("/{patient_id}", response_model=PatientRead)  
def update_patient_route(  
    patient_id: int,  
    patient: PatientUpdate,  
    db: Session = Depends(get_db)  
):  
    db_patient = crud.get_patient(db, patient_id)  
    if not db_patient:  
        raise HTTPException(status_code=404, detail="Patient not found")  
    return crud.update_patient(db, db_patient, patient)

@router.delete("/{patient_id}", status_code=204)  
def delete_patient_route(patient_id: int, db: Session = Depends(get_db)):  
    db_patient = crud.get_patient(db, patient_id)  
    if not db_patient:  
        raise HTTPException(status_code=404, detail="Patient not found")  
    db.delete(db_patient)  
    db.commit()  
    return {"detail": "Patient deleted successfully"}  # Return a success message

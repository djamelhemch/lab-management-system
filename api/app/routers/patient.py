from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from sqlalchemy import func  # <-- import func here

from app.database import SessionLocal, get_db
from app.schemas.patient import PatientCreate, PatientRead
from app.crud import patient as crud
from app.models.patient import Patient  # Import your ORM Patient model


router = APIRouter(prefix="/patients", tags=["Patients"])



@router.post("/", response_model=PatientRead)
def create_patient_route(patient: PatientCreate, db: Session = Depends(get_db)):
    return crud.create_patient(db, patient)

@router.get("/", response_model=list[PatientRead])
def list_patients_route(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    return crud.get_patients(db, skip, limit)

@router.get("/{patient_id}", response_model=PatientRead)
def get_patient_route(patient_id: int, db: Session = Depends(get_db)):
    db_patient = crud.get_patient(db, patient_id)
    if not db_patient:
        raise HTTPException(status_code=404, detail="Patient not found")
    return db_patient


from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from sqlalchemy import func
from typing import List
import datetime as dt

from app.database import get_db
from app.schemas.doctor import DoctorCreate, DoctorRead,DoctorUpdate
from app.crud import doctor as crud
from app.models.doctor import Doctor
from app.models.patient import Patient
from app.schemas.patient import PatientListItem

router = APIRouter(prefix="/doctors", tags=["Doctors"])


@router.get("/", response_model=List[DoctorRead])
def list_doctors_route(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    return crud.get_doctors_with_patient_count(db, skip, limit)

@router.get("/", response_model=List[DoctorRead])
def list_doctors_route(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    return crud.get_doctors(db, skip, limit)

@router.get("/{doctor_id}", response_model=DoctorRead)
def get_doctor_route(doctor_id: int, db: Session = Depends(get_db)):
    db_doctor = crud.get_doctor(db, doctor_id)
    if not db_doctor:
        raise HTTPException(status_code=404, detail="Doctor not found")
    return db_doctor



@router.get("/{doctor_id}/patients", response_model=List[PatientListItem])  
def get_doctor_patients_route(doctor_id: int, db: Session = Depends(get_db)):  
    patients = db.query(Patient).filter(Patient.doctor_id == doctor_id).all()  

    patient_list = []  
    for patient in patients:  
        # Calculate full_name  
        full_name = f"{patient.first_name or ''} {patient.last_name or ''}".strip()  

        # Calculate age from date of birth  
        age = None  
        if patient.dob:  
            today = dt.date.today()  
            age = today.year - patient.dob.year - ((today.month, today.day) < (patient.dob.month, patient.dob.day))  

        # Create patient list item  
        patient_item = PatientListItem(  
            id=patient.id,  
            full_name=full_name,  
            file_number=patient.file_number,  
            blood_type=patient.blood_type,  
            phone=patient.phone,  
            dob=patient.dob,  
            age=age  
        )  
        patient_list.append(patient_item)  

    return patient_list

@router.put("/{doctor_id}", response_model=DoctorRead)  
def update_doctor_route(doctor_id: int, doctor_update: DoctorUpdate, db: Session = Depends(get_db)):  
    db_doctor = crud.get_doctor(db, doctor_id)  
    if not db_doctor:  
        raise HTTPException(status_code=404, detail="Doctor not found")  
    updated_doctor = crud.update_doctor(db, doctor_id, doctor_update)  
    return updated_doctor
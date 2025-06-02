from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from sqlalchemy import func
from typing import List

from app.database import get_db
from app.schemas.doctor import DoctorCreate, DoctorRead
from app.crud import doctor as crud
from app.models.doctor import Doctor

router = APIRouter(prefix="/doctors", tags=["Doctors"])

@router.post("/", response_model=DoctorRead)
def create_doctor_route(doctor: DoctorCreate, db: Session = Depends(get_db)):
    return crud.create_doctor(db, doctor)

@router.get("/", response_model=List[DoctorRead])
def list_doctors_route(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    return crud.get_doctors(db, skip, limit)

@router.get("/{doctor_id}", response_model=DoctorRead)
def get_doctor_route(doctor_id: int, db: Session = Depends(get_db)):
    db_doctor = crud.get_doctor(db, doctor_id)
    if not db_doctor:
        raise HTTPException(status_code=404, detail="Doctor not found")
    return db_doctor



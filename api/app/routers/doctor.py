from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from sqlalchemy import func, or_
from typing import List
import datetime as dt

from app.database import get_db
from app.schemas.doctor import DoctorCreate, DoctorRead,DoctorUpdate
from app.crud import doctor as crud
from app.models.doctor import Doctor
from app.models.patient import Patient
from app.schemas.patient import PatientListItem

router = APIRouter(prefix="/doctors", tags=["Doctors"])
# -----------------------------
# LIST DOCTORS (default)
# -----------------------------
@router.get("/", response_model=List[DoctorRead])
def list_doctors_route(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    return crud.get_doctors(db, skip, limit)


# -----------------------------
# CREATE DOCTOR (NEEDED for POST /doctors/)
# -----------------------------
@router.post("/", response_model=DoctorRead, status_code=201)
def create_doctor_route(doctor: DoctorCreate, db: Session = Depends(get_db)):
    return crud.create_doctor(db, doctor)


# -----------------------------
# DOCTOR TABLE (Searchable)
# -----------------------------
@router.get("/table", response_model=List[DoctorRead])
def list_doctors_table_route(
    skip: int = 0,
    limit: int = 100,
    q: str = None,
    db: Session = Depends(get_db)
):
    query = db.query(Doctor)

    if q:
        q_like = f"%{q}%"
        query = query.filter(
            or_(
                Doctor.full_name.ilike(q_like),
                Doctor.specialty.ilike(q_like),
                Doctor.phone.ilike(q_like),
                Doctor.email.ilike(q_like)
            )
        )

    return query.order_by(Doctor.full_name.asc()).offset(skip).limit(limit).all()


# -----------------------------
# GET ONE DOCTOR
# -----------------------------
@router.get("/{doctor_id}", response_model=DoctorRead)
def get_doctor_route(doctor_id: int, db: Session = Depends(get_db)):
    doctor = crud.get_doctor(db, doctor_id)
    if not doctor:
        raise HTTPException(status_code=404, detail="Doctor not found")
    return doctor


# -----------------------------
# GET DOCTOR'S PATIENTS
# -----------------------------
@router.get("/{doctor_id}/patients", response_model=List[PatientListItem])
def get_doctor_patients_route(doctor_id: int, db: Session = Depends(get_db)):
    patients = db.query(Patient).filter(Patient.doctor_id == doctor_id).all()

    result = []
    for p in patients:
        full_name = f"{p.first_name or ''} {p.last_name or ''}".strip()

        # Age calculation
        age = None
        if p.dob:
            today = dt.date.today()
            age = today.year - p.dob.year - (
                (today.month, today.day) < (p.dob.month, p.dob.day)
            )

        result.append(
            PatientListItem(
                id=p.id,
                full_name=full_name,
                file_number=p.file_number,
                blood_type=p.blood_type,
                phone=p.phone,
                dob=p.dob,
                age=age
            )
        )

    return result


# -----------------------------
# PATIENT TABLE (searchable)
# -----------------------------
@router.get("/{doctor_id}/patients/table", response_model=List[PatientListItem])
def list_doctor_patients_table_route(
    doctor_id: int,
    skip: int = 0,
    limit: int = 100,
    q: str = None,
    db: Session = Depends(get_db)
):
    query = db.query(Patient).filter(Patient.doctor_id == doctor_id)

    if q:
        q_like = f"%{q}%"
        query = query.filter(
            or_(
                Patient.first_name.ilike(q_like),
                Patient.last_name.ilike(q_like),
                Patient.file_number.ilike(q_like),
                Patient.phone.ilike(q_like),
            )
        )

    patients = query.order_by(
        Patient.last_name.asc(),
        Patient.first_name.asc()
    ).offset(skip).limit(limit).all()

    result = []
    for p in patients:
        full_name = f"{p.first_name or ''} {p.last_name or ''}".strip()

        age = None
        if p.dob:
            today = dt.date.today()
            age = today.year - p.dob.year - (
                (today.month, today.day) < (p.dob.month, p.dob.day)
            )

        result.append(
            PatientListItem(
                id=p.id,
                full_name=full_name,
                file_number=p.file_number,
                blood_type=p.blood_type,
                phone=p.phone,
                dob=p.dob,
                age=age
            )
        )

    return result


# -----------------------------
# UPDATE DOCTOR
# -----------------------------
@router.put("/{doctor_id}", response_model=DoctorRead)
def update_doctor_route(doctor_id: int, doctor_update: DoctorUpdate, db: Session = Depends(get_db)):
    existing = crud.get_doctor(db, doctor_id)
    if not existing:
        raise HTTPException(status_code=404, detail="Doctor not found")

    return crud.update_doctor(db, doctor_id, doctor_update)

from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from sqlalchemy import func
from datetime import date

from app.database import get_db
from app.models.patient import Patient
from app.models.doctor import Doctor
from app.models.sample import Sample

router = APIRouter(prefix="/dashboard", tags=["Dashboard"])

@router.get("/metrics")
def get_dashboard_metrics(db: Session = Depends(get_db)):
    patients_count = db.query(func.count()).select_from(Patient).scalar()
    doctors_count = db.query(func.count()).select_from(Doctor).scalar()
    samples_today = db.query(func.count()).select_from(Sample).filter(func.date(Sample.collection_date) == date.today()).scalar()

    return {
        "patients_count": patients_count,
        "doctors_count": doctors_count,
        "samples_today": samples_today,
    }

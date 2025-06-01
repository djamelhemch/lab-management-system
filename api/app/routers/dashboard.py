from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from sqlalchemy import func
from datetime import date
from typing import List, Dict, Any
from sqlalchemy import func, desc
from datetime import datetime,timezone

from app.database import get_db
from app.models.patient import Patient
from app.models.doctor import Doctor
from app.models.sample import Sample

router = APIRouter(prefix="/dashboard", tags=["Dashboard"])

@router.get("/metrics")  
def get_dashboard_metrics(db: Session = Depends(get_db)):  
    patients_count = db.query(func.count(Patient.id)).scalar()  
    doctors_count = db.query(func.count(Doctor.id)).scalar()  
    samples_today = db.query(func.count()).select_from(Sample).filter(  
        func.date(Sample.collection_date) == date.today()  
    ).scalar()  

    # If you have a Report model, use this:  
    # pending_reports = db.query(func.count(Report.id)).filter(Report.status == 'pending').scalar()  
    pending_reports = 0  # Placeholder  

    return {  
        "patients_count": patients_count,  
        "doctors_count": doctors_count,  
        "samples_today": samples_today,  
        "pending_reports": pending_reports,  
    }

@router.get("/recent-activities")  
def get_recent_activities(db: Session = Depends(get_db)) -> List[Dict[str, Any]]:  
    activities = []  

    # Get recent samples (last 5)  
    recent_samples = db.query(Sample).order_by(desc(Sample.collection_date)).limit(5).all()  
    for sample in recent_samples:  
        time_diff = datetime.now() - sample.collection_date  
        if time_diff.days == 0:  
            time_str = f"{time_diff.seconds // 3600}h ago" if time_diff.seconds >= 3600 else f"{time_diff.seconds // 60}m ago"  
        else:  
            time_str = f"{time_diff.days}d ago"  

        activities.append({  
            "description": f"Sample collected for patient {sample.patient.first_name} {sample.patient.last_name}",  
            "time": time_str,  
            "color": "blue"  
        })  

    # Get recent patients (last 3)  
    recent_patients = db.query(Patient).order_by(desc(Patient.created_at)).limit(3).all()  
    for patient in recent_patients:  
        time_diff = datetime.now() - patient.created_at  
        if time_diff.days == 0:  
            time_str = f"{time_diff.seconds // 3600}h ago" if time_diff.seconds >= 3600 else f"{time_diff.seconds // 60}m ago"  
        else:  
            time_str = f"{time_diff.days}d ago"  

        activities.append({  
            "description": f"New patient registered: {patient.first_name} {patient.last_name}",  
            "time": time_str,  
            "color": "green"  
        })  

    # Sort by most recent and limit to 8 items  
    activities.sort(key=lambda x: x['time'])  
    return activities[:8]  

@router.get("/recent-patients")  
def get_recent_patients(db: Session = Depends(get_db)) -> List[Dict[str, Any]]:  
    # Get the 10 most recent patients  
    recent_patients = db.query(Patient).order_by(desc(Patient.created_at)).limit(10).all()  

    patients_data = []  
    for patient in recent_patients:  
        # Get the doctor name if available  
        doctor_name = None  
        if hasattr(patient, 'doctor') and patient.doctor:  
            doctor_name = f"Dr. {patient.doctor.full_name}"  
        elif hasattr(patient, 'doctor_id') and patient.doctor_id:  
            doctor = db.query(Doctor).filter(Doctor.id == patient.doctor_id).first()  
            if doctor:  
                doctor_name = f"Dr. {doctor.full_name}" 

        # Determine status (you might need to adjust this based on your business logic)  
        status = "Active"  # Default status  
        # If you have a status field in Patient model:  
        # status = patient.status if hasattr(patient, 'status') else "Active"  

        patients_data.append({  
            "file_number": patient.file_number,  
            "first_name": patient.first_name,  
            "last_name": patient.last_name,  
            "doctor_name": doctor_name,  
            "status": status  
        })  

    return patients_data





def humanize_time(dt):  
    now = datetime.now(timezone.utc)  
    diff = now - dt  
    if diff.days > 0:  
        return f"{diff.days}d ago"  
    hours = diff.seconds // 3600  
    if hours > 0:  
        return f"{hours}h ago"  
    minutes = diff.seconds // 60  
    if minutes > 0:  
        return f"{minutes}m ago"  
    return "Just now"
from fastapi import APIRouter, Depends, HTTPException, Query, Request
from sqlalchemy.orm import Session,joinedload
from sqlalchemy import func, or_  
from typing import List 
from pydantic import ValidationError
from app.schemas.patient import PatientBase
from app.database import SessionLocal, get_db
from app.schemas.patient import PatientCreate, PatientRead, PatientUpdate
from app.crud import patient as crud
from app.models.patient import Patient  # Import your ORM Patient model
from datetime import datetime
from sqlalchemy import desc
from datetime import date
from typing import Optional
from app.routers.auth import get_current_user
import logging
from app.utils.logging import log_action, log_route
from app.models.lab_result import LabResult
from app.models.quotation import Quotation, QuotationItem
from app.models.analysis import AnalysisCatalog, CategoryAnalyse, Unit
from collections import defaultdict

logger = logging.getLogger("uvicorn.error")
router = APIRouter(prefix="/patients", tags=["Patients"])

def calculate_age(date_of_birth):  
    """Calculate age from date of birth."""
    today = date.today()  
    age = today.year - date_of_birth.year - ((today.month, today.day) < (date_of_birth.month, date_of_birth.day))  
    return age

@router.post("/", response_model=PatientRead)  
@log_route("create_patient")
def create_patient_route(patient: PatientBase,current_user=Depends(get_current_user), request: Request = None, db: Session = Depends(get_db)):  
    try:  
        # Get the last file_number  
        last_patient = db.query(Patient).filter(Patient.file_number != None).order_by(desc(Patient.id)).first()  
        if last_patient and last_patient.file_number:  
            # Extract the numeric part and increment  
            last_num = int(last_patient.file_number[5:])  
            next_num = last_num + 1  
        else:  
            next_num = 1  
        # Format: PYYYYNNN (e.g., P2025002)  
        year = datetime.now().year  
        file_number = f"P{year}{str(next_num).zfill(3)}"  

        # Create a dict from patient, add file_number  
        patient_data = patient.dict()  
        patient_data['file_number'] = file_number  

        result = crud.create_patient(db, PatientCreate(**patient_data))  

        return result  
    except Exception as e:  
        print("Other error:", str(e))  
        raise HTTPException(status_code=500, detail=str(e))

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
        data['doctor_name'] = p.doctor.full_name if p.doctor else None  
        data['age'] = calculate_age(p.dob)  
        result.append(data) 
    return result
    
@router.get("/table", response_model=List[PatientRead])
def list_patients_table_route(
    skip: int = 0,
    limit: int = 100,
    q: Optional[str] = Query(None),
    db: Session = Depends(get_db)
):
    logger.info(f"=== GET /patients/table called ===")
    logger.info(f"Raw params - skip: {skip} (type: {type(skip)})")
    logger.info(f"Raw params - limit: {limit} (type: {type(limit)})")
    logger.info(f"Raw params - q: '{q}' (type: {type(q)})")

    try:
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
            data['age'] = calculate_age(p.dob)
            result.append(data)
        logger.info(f"=== GET /patients/table completed successfully ===")
        return result
    except Exception as e:
        logger.error(f"=== ERROR in list_patients_table_route ===")
        logger.error(f"Exception type: {type(e)}")
        logger.error(f"Exception message: {str(e)}")
        logger.error(f"Exception details:", exc_info=True)
        raise HTTPException(status_code=500, detail=f"Internal server error: {str(e)}")
    
@router.get("/{patient_id}", response_model=PatientRead)  
def get_patient_route(patient_id: int, db: Session = Depends(get_db)):  
    db_patient = crud.get_patient(db, patient_id)  
    if not db_patient:  
        raise HTTPException(status_code=404, detail="Patient not found")  

    # Convert to dictionary and add age  
    patient_data = db_patient.__dict__.copy()  
    patient_data['age'] = calculate_age(db_patient.dob)  

    return PatientRead(**patient_data)

from datetime import datetime

@router.get("/{patient_id}/results")
def get_patient_results(patient_id: int, db: Session = Depends(get_db)):

    # 1️⃣ Fetch patient
    patient = db.query(Patient).filter(Patient.id == patient_id).first()
    if not patient:
        raise HTTPException(404, "Patient not found")

    # 2️⃣ Fetch all lab results using joinedload for relationships
    results = (
        db.query(LabResult)
        .options(
            joinedload(LabResult.quotation_item)
            .joinedload(QuotationItem.analysis)
            .joinedload(AnalysisCatalog.unit),
            joinedload(LabResult.quotation_item)
            .joinedload(QuotationItem.analysis)
            .joinedload(AnalysisCatalog.category_analyse),
            joinedload(LabResult.quotation)  # for patient filter if needed
        )
        .join(Quotation, Quotation.id == LabResult.quotation_id)
        .filter(Quotation.patient_id == patient_id)
        .order_by(LabResult.created_at.desc())
        .all()
    )

    grouped = defaultdict(lambda: defaultdict(list))
    years = set()
    all_dates = {}

    for lab in results:
        item = lab.quotation_item
        analysis: AnalysisCatalog = item.analysis
        category: CategoryAnalyse = analysis.category_analyse
        unit = analysis.unit

        category_name = category.name
        analysis_name = analysis.name

        result_obj = {
            "result_id": lab.id,
            "value": lab.result_value,
            "interpretation": lab.interpretation,
            "date": lab.created_at.isoformat() if lab.created_at else None,
            "device": lab.device_name,
            "normal_min": lab.normal_min,
            "normal_max": lab.normal_max,
            "unit": unit.name if unit else None
        }

        grouped[category_name][analysis_name].append(result_obj)

        if lab.created_at:
            years.add(lab.created_at.year)
            date_key = lab.created_at.strftime('%Y-%m-%d %H:%M:%S')
            if date_key not in all_dates:
                all_dates[date_key] = {
                    "display": lab.created_at.strftime('%d/%m/%Y'),
                    "time": lab.created_at.strftime('%H:%M'),
                    "year": lab.created_at.year,
                }

    years_sorted = sorted(list(years), reverse=True)
    dates_sorted = dict(sorted(all_dates.items(), reverse=True))

    patient_payload = {
        "id": patient.id,
        "file_number": patient.file_number,
        "first_name": patient.first_name,
        "last_name": patient.last_name,
        "dob": str(patient.dob) if patient.dob else None,
        "gender": patient.gender,
        "blood_type": patient.blood_type,
        "weight": patient.weight,
        "height": getattr(patient, "height", None),
        "phone": getattr(patient, "phone", None),
        "email": getattr(patient, "email", None),
        "address": getattr(patient, "address", None),
        "allergies": getattr(patient, "allergies", None),
        "chronic_diseases": getattr(patient, "chronic_conditions", None),
        "antecedents": getattr(patient, "antecedents", None),
        "medication": getattr(patient, "medication", None),
    }

    return {
        "patient": patient_payload,
        "categories": grouped,
        "years": years_sorted,
        "dates": dates_sorted
    }


@router.put("/{patient_id}", response_model=PatientRead)  
@log_route("update_patient")
def update_patient_route(  
    patient_id: int,  
    patient: PatientUpdate,  
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
    request: Request = None
):  
    db_patient = crud.get_patient(db, patient_id)  
    if not db_patient:  
        raise HTTPException(status_code=404, detail="Patient not found")  

    return crud.update_patient(db, db_patient, patient)

@router.delete("/{patient_id}", status_code=204)  
@log_route("delete_patient")
def delete_patient_route(patient_id: int, db: Session = Depends(get_db),current_user=Depends(get_current_user),
    request: Request = None):  
    db_patient = crud.get_patient(db, patient_id)  
    if not db_patient:  
        raise HTTPException(status_code=404, detail="Patient not found")  

    db.delete(db_patient)  
    db.commit()  
    return {"detail": "Patient deleted successfully"}  # Return a success message



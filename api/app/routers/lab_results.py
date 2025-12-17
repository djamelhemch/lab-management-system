from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from app.database import get_db
from app.schemas.lab_result import LabResultCreate, LabResultResponse, BulkLabResultCreate
from app.crud import lab_result
from app.models.patient import Patient
from app.models.analysis import NormalRange
from typing import List, Union    
from app.crud import lab_result as lab_result_crud
from app.models.quotation import Quotation, QuotationItem
from datetime import date
from app.models.lab_result import LabResult
router = APIRouter(prefix="/lab-results", tags=["Lab Results"])

def enrich_lab_result(result: LabResult, db: Session) -> LabResultResponse:
    """
    Convert LabResult ORM → LabResultResponse with extra info:
    - analysis_name, analysis_code
    - patient_first_name, patient_last_name, file_number
    """
    analysis_name = None
    analysis_code = None
    patient_first_name = None
    patient_last_name = None
    file_number = None

    if result.quotation_item_id:
        quotation_item = db.query(QuotationItem).filter(
            QuotationItem.id == result.quotation_item_id
        ).first()

        if quotation_item:
            analysis = quotation_item.analysis
            if analysis:
                analysis_name = analysis.name
                analysis_code = analysis.code

            if quotation_item.quotation and quotation_item.quotation.patient:
                patient = quotation_item.quotation.patient
                patient_first_name = patient.first_name
                patient_last_name = patient.last_name
                file_number = patient.file_number

    return LabResultResponse(
        id=result.id,
        quotation_id=result.quotation_id,
        quotation_item_id=result.quotation_item_id,
        result_value=result.result_value,
        interpretation=result.interpretation,
        status=result.status,
        device_name=result.device_name,
        normal_min=result.normal_min,
        normal_max=result.normal_max,
        analysis_name=analysis_name,
        analysis_code=analysis_code,
        patient_first_name=patient_first_name,
        patient_last_name=patient_last_name,
        file_number=file_number,
        normal_range_id=result.normal_range_id,
        created_at=result.created_at,
    )

    
@router.get("/", response_model=List[LabResultResponse])
def list_lab_results(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    results = db.query(LabResult).offset(skip).limit(limit).all()  # ORM objects
    return [enrich_lab_result(r, db) for r in results]


@router.get("/{result_id}", response_model=LabResultResponse)
def get_lab_result(result_id: int, db: Session = Depends(get_db)):
    result = db.query(LabResult).filter(LabResult.id == result_id).first()
    if not result:
        raise HTTPException(status_code=404, detail="Lab result not found")
    return enrich_lab_result(result, db)


@router.post("/", response_model=LabResultResponse)
def create_lab_result_endpoint(data: LabResultCreate, db: Session = Depends(get_db)):
    try:
        result = lab_result_crud.create_lab_result(db, data)
        return enrich_lab_result(result, db)
    except ValueError as e:
        raise HTTPException(status_code=404, detail=str(e))


@router.post("/bulk", response_model=List[LabResultResponse])
def create_lab_results_bulk_endpoint(data: BulkLabResultCreate, db: Session = Depends(get_db)):
    try:
        results = lab_result_crud.create_lab_results_for_quotation(
            db, quotation_id=data.quotation_id, result_values=data.result_values
        )
        return [enrich_lab_result(r, db) for r in results]

    except ValueError as e:
        raise HTTPException(status_code=404, detail=str(e))
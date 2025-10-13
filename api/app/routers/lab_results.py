from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from app.database import get_db
from app.schemas.lab_result import LabResultCreate, LabResultResponse
from app.crud import lab_result
from app.models.patient import Patient
from app.models.analysis import NormalRange
from typing import List
from app.crud import lab_result as lab_result_crud
from app.models.quotation import Quotation, QuotationItem
from datetime import date

router = APIRouter(prefix="/lab-results", tags=["Lab Results"])


@router.post("/", response_model=LabResultResponse)
def create_lab_result(data: LabResultCreate, db: Session = Depends(get_db)):
    try:
        result = lab_result_crud.create_lab_result(db, data)
        return LabResultResponse.from_orm(result)
    except ValueError as e:
        raise HTTPException(status_code=404, detail=str(e))


@router.get("/", response_model=List[LabResultResponse])
def list_lab_results(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    results = lab_result_crud.list_lab_results(db, skip, limit)
    # ✅ Convert dicts → validated Pydantic models
    return [LabResultResponse.model_validate(r) for r in results]


@router.get("/{result_id}")
def get_lab_result(result_id: int, db: Session = Depends(get_db)):
    result = lab_result_crud.get_lab_result(db, result_id)
    if not result:
        raise HTTPException(status_code=404, detail="Lab result not found")
    return result
from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from typing import List
from app.db import get_db
from app.schemas.analysis_request import (
    AnalysisRequestResponse,
    AnalysisRequestCreate,
    AnalysisRequestUpdate,
)
from app.crud import analysis_request as crud

router = APIRouter(prefix="/analysis-requests", tags=["Analysis Requests"])

@router.get("/", response_model=List[AnalysisRequestResponse])
def list_analysis_requests(skip: int = 0, limit: int = 50, db: Session = Depends(get_db)):
    return crud.get_analysis_requests(db, skip=skip, limit=limit)

@router.post("/", response_model=AnalysisRequestResponse)
def create_analysis_request(data: AnalysisRequestCreate, db: Session = Depends(get_db)):
    return crud.create_analysis_request(db, data)

@router.get("/{id}", response_model=AnalysisRequestResponse)
def get_analysis_request(id: int, db: Session = Depends(get_db)):
    obj = crud.get_analysis_request(db, id)
    if not obj:
        raise HTTPException(status_code=404, detail="Analysis request not found")
    return obj

@router.put("/{id}", response_model=AnalysisRequestResponse)
def update_analysis_request(id: int, data: AnalysisRequestUpdate, db: Session = Depends(get_db)):
    obj = crud.update_analysis_request(db, id, data)
    if not obj:
        raise HTTPException(status_code=404, detail="Analysis request not found")
    return obj

@router.delete("/{id}")
def delete_analysis_request(id: int, db: Session = Depends(get_db)):
    success = crud.delete_analysis_request(db, id)
    if not success:
        raise HTTPException(status_code=404, detail="Analysis request not found")
    return {"message": "Analysis request deleted successfully"}

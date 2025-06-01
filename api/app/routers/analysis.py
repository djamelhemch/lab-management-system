# routers/analysis.py
from fastapi import APIRouter, Depends, HTTPException, Query
from sqlalchemy.orm import Session
from typing import List, Optional
from app.database import get_db
from app.schemas.analysis import AnalysisCreate, AnalysisUpdate, AnalysisResponse
from app.crud.analysis import analysis_crud

router = APIRouter(prefix="/analyses", tags=["analyses"])

@router.post("/", response_model=AnalysisResponse)
def create_analysis(analysis: AnalysisCreate, db: Session = Depends(get_db)):
    # Check if code already exists
    if analysis.code and analysis_crud.get_by_code(db, analysis.code):
        raise HTTPException(status_code=400, detail="Analysis code already exists")

    return analysis_crud.create(db, analysis)

@router.get("/", response_model=List[AnalysisResponse])
def get_analyses(
    skip: int = 0,
    limit: int = 100,
    category: Optional[str] = Query(None),
    search: Optional[str] = Query(None),
    db: Session = Depends(get_db)
):
    return analysis_crud.get_all(db, skip=skip, limit=limit, category=category, search=search)

@router.get("/categories", response_model=List[str])
def get_categories(db: Session = Depends(get_db)):
    return analysis_crud.get_categories(db)

@router.get("/{analysis_id}", response_model=AnalysisResponse)
def get_analysis(analysis_id: int, db: Session = Depends(get_db)):
    analysis = analysis_crud.get(db, analysis_id)
    if not analysis:
        raise HTTPException(status_code=404, detail="Analysis not found")
    return analysis

@router.put("/{analysis_id}", response_model=AnalysisResponse)
def update_analysis(analysis_id: int, analysis: AnalysisUpdate, db: Session = Depends(get_db)):
    updated_analysis = analysis_crud.update(db, analysis_id, analysis)
    if not updated_analysis:
        raise HTTPException(status_code=404, detail="Analysis not found")
    return updated_analysis

@router.delete("/{analysis_id}")
def delete_analysis(analysis_id: int, db: Session = Depends(get_db)):
    if not analysis_crud.delete(db, analysis_id):
        raise HTTPException(status_code=404, detail="Analysis not found")
    return {"message": "Analysis deleted successfully"}
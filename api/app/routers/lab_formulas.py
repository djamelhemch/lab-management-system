from fastapi import APIRouter, Depends, HTTPException, Request
from sqlalchemy.orm import Session
from datetime import datetime
from typing import List
from app.database import get_db
from app.models.lab_formulas import LabFormula
from pydantic import BaseModel
from app.routers.auth import get_current_user
from app.utils.app_logging import log_action, log_route  
from app.models.user import User
router = APIRouter(prefix="/lab-formulas", tags=["Lab Formulas"])

class FormulaBase(BaseModel):
    name: str
    formula: str

class FormulaResponse(FormulaBase):
    id: int
    created_at: datetime

    class Config:
        orm_mode = True


@router.post("/", response_model=FormulaResponse)
@log_route("create_formula")
def create_formula(
    data: FormulaBase,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    request: Request = None
):
    try:
        formula = LabFormula(
            name=data.name,
            formula=data.formula,
            created_by=current_user["id"]  # 👈 take from authenticated user
        )
        db.add(formula)
        db.commit()
        db.refresh(formula)
        return formula
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail=str(e))


@router.get("/", response_model=List[FormulaResponse])
def list_formulas(db: Session = Depends(get_db)):
    return db.query(LabFormula).order_by(LabFormula.created_at.desc()).all()


@router.delete("/{formula_id}")
def delete_formula(formula_id: int, db: Session = Depends(get_db)):
    formula = db.query(LabFormula).filter(LabFormula.id == formula_id).first()
    if not formula:
        raise HTTPException(status_code=404, detail="Formula not found")
    db.delete(formula)
    db.commit()
    return {"message": "Deleted successfully"}

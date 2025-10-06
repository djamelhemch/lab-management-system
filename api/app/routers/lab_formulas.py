from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from datetime import datetime
from typing import List
from app.database import get_db
from app.models.lab_formulas import LabFormula
from pydantic import BaseModel

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
def create_formula(data: FormulaBase, db: Session = Depends(get_db)):
    formula = LabFormula(name=data.name, formula=data.formula)
    db.add(formula)
    db.commit()
    db.refresh(formula)
    return formula


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

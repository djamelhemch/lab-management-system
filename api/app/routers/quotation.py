from fastapi import APIRouter, Depends, HTTPException, status, Request
from sqlalchemy.orm import Session, joinedload
from app.database import get_db
from app.crud import quotation as crud_quotation
from app.models.quotation import Quotation
from app.schemas.quotation import QuotationWithPatientSchema, QuotationCreate, QuotationStatusEnum, QuotationItem
from typing import List
from app.crud.quotation import get_revenue_stats
from app.routers.auth import get_current_user  # Add this import
from app.utils.logging import log_route  # Import the logging decorator
from app.models.user import User  # Import User model for current_user dependency
router = APIRouter(prefix="/quotations", tags=["Quotations"])


@router.get("/stats")
def quotation_stats(db: Session = Depends(get_db)):
    return get_revenue_stats(db)

@router.get("/", response_model=List[QuotationWithPatientSchema])
def list_quotations(db: Session = Depends(get_db)):
    quotations = db.query(Quotation).options(
        joinedload(Quotation.patient),
        joinedload(Quotation.analysis_items)
    ).all()
    return quotations

@router.get("/{quotation_id}", response_model=QuotationWithPatientSchema)
@log_route("read_quotation")
def read_quotation(quotation_id: int, db: Session = Depends(get_db), current_user: User = Depends(get_current_user),
    request: Request = None):
    quotation = crud_quotation.get_quotation(db, quotation_id)
    if not quotation:
        raise HTTPException(status_code=404, detail="Quotation not found")
    return quotation

@router.post("/", response_model=QuotationWithPatientSchema)
@log_route("create_quotation")
def create_quotation(quotation_in: QuotationCreate, db: Session = Depends(get_db),current_user: User = Depends(get_current_user),
    request: Request = None):
    db_quotation = crud_quotation.create_quotation(db, quotation_in)
    return db_quotation

@router.put("/{quotation_id}/convert", response_model=QuotationWithPatientSchema)
@log_route("convert_quotation")
def convert_quotation_route(quotation_id: int, db: Session = Depends(get_db)):
    return crud_quotation.convert_quotation(db, quotation_id)

@router.delete("/{quotation_id}", status_code=status.HTTP_204_NO_CONTENT)
@log_route("delete_quotation")
def delete_quotation(quotation_id: int, db: Session = Depends(get_db), current_user: User = Depends(get_current_user),
    request: Request = None):
    quotation = crud_quotation.get_quotation(db, quotation_id)
    if not quotation:
        raise HTTPException(status_code=404, detail="Quotation not found")

    db.delete(quotation)
    db.commit()
    return
from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session, joinedload
from app.database import get_db
from app.crud import quotation as crud_quotation
from app.models.quotation import Quotation  
from app.schemas.quotation import QuotationWithPatientSchema, QuotationCreate,QuotationBase
from typing import List

router = APIRouter(prefix="/quotations", tags=["Quotations"])


@router.get("/", response_model=List[QuotationWithPatientSchema])
def list_quotations(db: Session = Depends(get_db)):
    quotations = db.query(Quotation)\
        .options(joinedload(Quotation.patient), joinedload(Quotation.items))\
        .all()

    return [
        {
            "id": q.id,
            "patient_id": q.patient_id,
            "status": q.status,
            "total": q.total,
            "net_total": q.net_total,  # add this line
            "created_at": q.created_at,
            "updated_at": q.updated_at,
            "items": [
                {
                    "id": item.id,
                    "analysis_id": item.analysis_id,
                    "price": item.price
                } for item in q.items
            ],
            "patient": {
                "full_name": f"{q.patient.first_name} {q.patient.last_name}",
                "file_number": q.patient.file_number
            } if q.patient else None
        } for q in quotations
    ]

@router.get("/{quotation_id}", response_model=QuotationWithPatientSchema)
def read_quotation(quotation_id: int, db: Session = Depends(get_db)):
    quotation = crud_quotation.get_quotation(db, quotation_id)
    if not quotation:
        raise HTTPException(status_code=404, detail="Quotation not found")
    return quotation

@router.post("/", response_model=QuotationWithPatientSchema)
def create_quotation(quotation_in: QuotationCreate, db: Session = Depends(get_db)):
    db_quotation = crud_quotation.create_quotation(db, quotation_in)

    return {
        "id": db_quotation.id,
        "patient_id": db_quotation.patient_id,
        "status": db_quotation.status,
        "total": db_quotation.total,
        "created_at": db_quotation.created_at,
        "updated_at": db_quotation.updated_at,
        "items": [
            {
                "id": item.id,
                "analysis_id": item.analysis_id,
                "price": item.price
            } for item in db_quotation.items
        ],
        "patient": {
            "full_name": f"{db_quotation.patient.first_name} {db_quotation.patient.last_name}" if db_quotation.patient else None,
            "file_number": db_quotation.patient.file_number if db_quotation.patient else None
        }
    }

@router.delete("/{quotation_id}", status_code=status.HTTP_204_NO_CONTENT)
def delete_quotation(quotation_id: int, db: Session = Depends(get_db)):
    quotation = crud_quotation.get_quotation(db, quotation_id)
    if not quotation:
        raise HTTPException(status_code=404, detail="Quotation not found")

    db.delete(quotation)
    db.commit()
    return
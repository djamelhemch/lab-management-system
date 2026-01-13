from fastapi import APIRouter, Depends, HTTPException, status, Request, Query
from sqlalchemy.orm import Session, joinedload
from sqlalchemy import or_
from app.database import get_db
from app.crud import quotation as crud_quotation
from app.models.quotation import Quotation
from app.schemas.quotation import QuotationWithPatientSchema, QuotationCreate, QuotationStatusEnum, QuotationItem
from app.schemas.quotation_dashboard import PatientTodayQuotations, QuotationTodayItem
from datetime import datetime
from typing import List, Optional
import math
from app.crud.quotation import get_revenue_stats
from app.routers.auth import get_current_user  # Add this import
from app.utils.app_logging import log_route  # Import the logging decorator
from app.models.user import User  # Import User model for current_user dependency
from app.models.patient import Patient  # Import Patient model for search
from sqlalchemy import func
import logging
router = APIRouter(prefix="/quotations", tags=["Quotations"])
logger = logging.getLogger("uvicorn") 

@router.get("/stats")
def quotation_stats(db: Session = Depends(get_db)):
    return get_revenue_stats(db)

@router.get("/today", response_model=list[dict])
def list_today_quotations_dashboard(
    db: Session = Depends(get_db),
    limit: int = Query(50, le=100),
):
    today = datetime.now().date()
    logger.info(f"Fetching today's quotations for {today} with limit {limit}")

    quotations = (
        db.query(Quotation)
        .options(joinedload(Quotation.patient))
        .filter(func.date(Quotation.created_at) == today)
        .order_by(Quotation.created_at.desc())
        .limit(limit)
        .all()
    )

    logger.info(f"Fetched {len(quotations)} quotations from DB")

    grouped: dict[int, dict] = {}
    for q in quotations:
        if not q.patient:
            logger.warning(f"Quotation {q.id} has no patient")
            continue

        pid = q.patient.id
        if pid not in grouped:
            grouped[pid] = {
                "patient_id": pid,
                "first_name": q.patient.first_name,
                "last_name": q.patient.last_name,
                "file_number": q.patient.file_number,
                "quotations": [],
            }
        grouped[pid]["quotations"].append({
            "quotation_id": q.id,
            "created_at": q.created_at.isoformat(),
        })

    grouped_list = list(grouped.values())
    logger.info(f"Returning {len(grouped_list)} patients with quotations")
    return grouped_list

@router.get("/", response_model=dict)  # return structured dict instead of plain list
def list_quotations(
    db: Session = Depends(get_db),
    q: Optional[str] = Query(None),
    status: Optional[str] = Query(None),
    page: int = 1,
    limit: int = 10,
):
    query = db.query(Quotation).options(
        joinedload(Quotation.patient),
        joinedload(Quotation.analysis_items)
    )

    # search by patient name or file number
    if q:
        search = f"%{q}%"
        query = query.join(Quotation.patient).filter(
            or_(
                Patient.first_name.ilike(search),
                Patient.last_name.ilike(search),
                Patient.file_number.ilike(search),
            )
        )

    # filter by status
    if status:
        query = query.filter(Quotation.status == status)

    # pagination
    total = query.count()
    items = query.offset((page - 1) * limit).limit(limit).all()

    items_schema = [QuotationWithPatientSchema.model_validate(item, from_attributes=True) for item in items]

    return {
        "items": items_schema,
        "total": total,
        "page": page,
        "last_page": math.ceil(total / limit) if total > 0 else 1,
    }

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
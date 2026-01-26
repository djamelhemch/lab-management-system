from sqlalchemy import func, or_
from datetime import timedelta
from sqlalchemy.orm import Session, joinedload
from app.models.quotation import Quotation, QuotationItem
from app.models.analysis import AnalysisCatalog
from app.models.agreement import Agreement
from app.models.queue import Queue, QueueType
from app.models.payment import Payment
from app.schemas.quotation import QuotationCreate, QuotationStatusEnum, QuotationWithPatientSchema
from fastapi import HTTPException
from datetime import datetime, timezone
import logging
from sqlalchemy.orm import joinedload
from decimal import Decimal, ROUND_HALF_UP

    
def create_quotation(db: Session, quotation_in: QuotationCreate):
    logging.info(f"Quotation payload: {quotation_in.dict()}")

    # --- 1. Prepare Quotation Items ---
    items_data = []
    for item in quotation_in.analysis_items:
        analysis = db.query(AnalysisCatalog).filter(AnalysisCatalog.id == item.analysis_id).first()
        if not analysis:
            raise HTTPException(status_code=404, detail=f"Analysis ID {item.analysis_id} not found")
        price = Decimal(str(item.price or 0)).quantize(Decimal("0.01"), rounding=ROUND_HALF_UP)
        items_data.append(QuotationItem(analysis_id=analysis.id, price=price))

    # --- 2. Create Quotation using frontend-calculated values ---
    quotation = Quotation(
        patient_id=quotation_in.patient_id,
        status=quotation_in.status,
        total=Decimal(str(quotation_in.total)).quantize(Decimal("0.01"), rounding=ROUND_HALF_UP),
        agreement_id=quotation_in.agreement_id,
        discount_applied=Decimal(str(quotation_in.discount_applied or 0)).quantize(Decimal("0.01"), rounding=ROUND_HALF_UP),
        net_total=Decimal(str(quotation_in.net_total)).quantize(Decimal("0.01"), rounding=ROUND_HALF_UP),
        outstanding=Decimal(str(quotation_in.outstanding)).quantize(Decimal("0.01"), rounding=ROUND_HALF_UP),
        analysis_items=items_data
    )
    db.add(quotation)
    db.commit()
    db.refresh(quotation)
    logging.info(f"Created quotation ID: {quotation.id}")

    # --- 3. Handle Initial Payment ---
    if quotation_in.payment:
        payment_data = quotation_in.payment
        payment = Payment(
            quotation_id=quotation.id,
            amount=float(payment_data.amount),
            method=payment_data.method,
            notes=payment_data.notes,
            user_id=payment_data.user_id,
            amount_received=float(payment_data.amount_received or 0.0),
            change_given=float(payment_data.change_given or 0.0),
        )
        db.add(payment)
        db.commit()
        db.refresh(payment)
        logging.info(f"Created payment ID: {payment.id}")

        # Update quotation outstanding if necessary
        db.refresh(quotation)
        quotation.outstanding = Decimal(str(quotation_in.outstanding)).quantize(Decimal("0.01"), rounding=ROUND_HALF_UP)
        db.commit()
        db.refresh(quotation)
        logging.info(f"Updated quotation outstanding: {quotation.outstanding}")

    return quotation
    
def get_quotation(db: Session, quotation_id: int):
    return (
        db.query(Quotation)
        .options(
            # load analysis items
            joinedload(Quotation.analysis_items)
            .joinedload(QuotationItem.analysis)  # load the analysis
            .joinedload(AnalysisCatalog.normal_ranges),  # load its normal ranges
            joinedload(Quotation.analysis_items)
            .joinedload(QuotationItem.analysis)
            .joinedload(AnalysisCatalog.unit),  # load the unit of the analysis
            joinedload(Quotation.patient),
            joinedload(Quotation.agreement),
            joinedload(Quotation.payments)
        )
        .filter(Quotation.id == quotation_id)
        .first()
    )
def get_all_quotations(db: Session, skip: int = 0, limit: int = 100):
    return (
        db.query(Quotation)
        .options(
            # Load analysis items with their analysis details
            joinedload(Quotation.analysis_items)
            .joinedload(QuotationItem.analysis)
            .joinedload(AnalysisCatalog.normal_ranges),
            
            joinedload(Quotation.analysis_items)
            .joinedload(QuotationItem.analysis)
            .joinedload(AnalysisCatalog.unit),
            
            # Load patient
            joinedload(Quotation.patient),
            
            # Load agreement
            joinedload(Quotation.agreement),
            
            # Load payments
            joinedload(Quotation.payments)
        )
        .offset(skip)
        .limit(limit)
        .all()
    )

def convert_quotation(db: Session, quotation_id: int):
    quotation = (
        db.query(Quotation)
        .options(
            joinedload(Quotation.analysis_items).joinedload(QuotationItem.analysis),
            joinedload(Quotation.patient)
        )
        .filter(Quotation.id == quotation_id)
        .first()
    )

    if not quotation:
        raise HTTPException(status_code=404, detail="Quotation not found")

    if quotation.status == QuotationStatusEnum.converted:
        raise HTTPException(status_code=400, detail="Quotation is already converted")

    # Update status and mark conversion time
    quotation.status = QuotationStatusEnum.converted
    quotation.updated_at = datetime.now(timezone.utc)  # conversion timestamp

    db.commit()
    db.refresh(quotation)

    return quotation

def get_revenue_stats(db: Session):
    now = datetime.utcnow()

    # Start of periods
    today_start = datetime(now.year, now.month, now.day)
    week_start = today_start - timedelta(days=now.weekday())
    month_start = datetime(now.year, now.month, 1)
    year_start = datetime(now.year, 1, 1)

    def sum_paid(since=None):
        q = db.query(func.coalesce(func.sum(Payment.amount), 0))
        if since:
            q = q.filter(Payment.paid_at >= since)
        return float(q.scalar())

    def outstanding_balance(since=None):
        q = (
            db.query(func.coalesce(func.sum(Quotation.net_total), 0) -
                     func.coalesce(func.sum(Payment.amount), 0))
            .outerjoin(Payment, Quotation.id == Payment.quotation_id)
            .filter(Quotation.status == "converted")
        )
        if since:
            q = q.filter(Quotation.updated_at >= since)
        return float(q.scalar())

    stats = {
        "paid": {
            "today": sum_paid(today_start),
            "week": sum_paid(week_start),
            "month": sum_paid(month_start),
            "year": sum_paid(year_start),
            "all_time": sum_paid(),
        },
        "outstanding": {
            "today": outstanding_balance(today_start),
            "week": outstanding_balance(week_start),
            "month": outstanding_balance(month_start),
            "year": outstanding_balance(year_start),
            "all_time": outstanding_balance(),
        }
    }

    return stats
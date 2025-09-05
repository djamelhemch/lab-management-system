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

def create_quotation(db: Session, quotation_in: QuotationCreate):


    logging.info(f"Quotation payload: {quotation_in.dict()}")

    # --- 1. Prepare Quotation Items ---
    items_data = []
    total = 0.0
    for item in quotation_in.analysis_items:   # ✅ must match Pydantic field
        analysis = db.query(AnalysisCatalog).filter(AnalysisCatalog.id == item.analysis_id).first()
        if not analysis:
            raise HTTPException(status_code=404, detail=f"Analysis ID {item.analysis_id} not found")
        price = float(item.price or analysis.price)
        total += price
        items_data.append(QuotationItem(analysis_id=analysis.id, price=price))

    # --- 2. Discount Logic ---
    discount = 0.0
    net_total = total
    if quotation_in.agreement_id:
        agreement = db.query(Agreement).filter(Agreement.id == quotation_in.agreement_id).first()
        if not agreement:
            raise HTTPException(status_code=404, detail="Agreement not found")
        if agreement.discount_type == "percentage":
            discount = (total * float(agreement.discount_value)) / 100
        elif agreement.discount_type == "fixed":
            discount = float(agreement.discount_value)
        net_total = total - discount

    # --- 3. Create Quotation ---
    quotation = Quotation(
        patient_id=quotation_in.patient_id,
        status=quotation_in.status,
        total=total,
        agreement_id=quotation_in.agreement_id,
        discount_applied=discount,
        net_total=net_total,
        analysis_items=items_data
    )
    db.add(quotation)
    db.commit()
    db.refresh(quotation)
    logging.info(f"Created quotation ID: {quotation.id}")

    # --- 4. Create Payment if Provided ---
    if quotation_in.payment:
        payment_data = quotation_in.payment
        payment = Payment(
            quotation_id=quotation.id,
            amount=float(payment_data.amount),
            method=payment_data.method,
            notes=payment_data.notes,
            user_id=payment_data.user_id,
            amount_received=float(payment_data.amount_received) if payment_data.amount_received else None,
            change_given=float(payment_data.change_given) if payment_data.change_given else None,
        )
        db.add(payment)
        db.commit()
        db.refresh(payment)
        logging.info(f"Created payment ID: {payment.id}")

    # --- 5. Queue Entry ---
    last_queue = db.query(Queue).order_by(Queue.position.desc()).first()
    next_queue_number = 1 if not last_queue else last_queue.position + 1

    queue_entry = Queue(
        patient_id=quotation_in.patient_id,
        quotation_id=quotation.id,
        type=QueueType.reception,
        position=next_queue_number,
        status="waiting",
        created_at=datetime.now(timezone.utc)
    )
    db.add(queue_entry)
    db.commit()
    db.refresh(queue_entry)
    logging.info(f"Added to queue position: {queue_entry.position}")

    # --- 6. Return Quotation with Payments ---
    quotation = (
        db.query(Quotation)
        .options(
            joinedload(Quotation.analysis_items).joinedload(QuotationItem.analysis),
            joinedload(Quotation.patient),
            joinedload(Quotation.payments) 
        )
        .filter(Quotation.id == quotation.id)
        .first()
    )
    return quotation

def get_quotation(db: Session, quotation_id: int):
    return (
        db.query(Quotation)
        .options(
            joinedload(Quotation.analysis_items).joinedload(QuotationItem.analysis),
            joinedload(Quotation.patient),
            joinedload(Quotation.agreement),
            joinedload(Quotation.payments)
        )
        .filter(Quotation.id == quotation_id)
        .first()
    )

def get_all_quotations(db: Session, skip: int = 0, limit: int = 100):
    return db.query(Quotation).offset(skip).limit(limit).all()

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
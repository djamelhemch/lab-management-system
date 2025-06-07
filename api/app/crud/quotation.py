from sqlalchemy.orm import Session
from app.models.quotation import Quotation, QuotationItem
from app.models.analysis import AnalysisCatalog
from app.models.agreement import Agreement
from app.models.queue import Queue, QueueType
from app.schemas.quotation import QuotationCreate
from fastapi import HTTPException
from datetime import datetime, timezone

def create_quotation(db: Session, quotation_in: QuotationCreate):
    items_data = []
    total = 0.0

    # Calculate total price from items
    for item in quotation_in.items:
        analysis = db.query(AnalysisCatalog).filter(AnalysisCatalog.id == item.analysis_id).first()
        if not analysis:
            raise HTTPException(status_code=404, detail=f"Analysis ID {item.analysis_id} not found")
        price = analysis.price
        total += price
        items_data.append(QuotationItem(analysis_id=analysis.id, price=price))

    # Discount logic
    discount = 0.0
    net_total = total
    agreement = None
    if quotation_in.agreement_id:
        agreement = db.query(Agreement).filter(Agreement.id == quotation_in.agreement_id).first()
        if not agreement:
            raise HTTPException(status_code=404, detail="Agreement not found")

        if agreement.discount_type == "percentage":
            discount = (total * float(agreement.discount_value)) / 100
        elif agreement.discount_type == "fixed":
            discount = float(agreement.discount_value)

        net_total = total - discount

    # Create quotation
    quotation = Quotation(
        patient_id=quotation_in.patient_id,
        status=quotation_in.status,
        total=total,
        agreement_id=quotation_in.agreement_id,
        discount_applied=discount,
        net_total=net_total,
        items=items_data
    )
    db.add(quotation)
    db.commit()
    db.refresh(quotation)

    # Now, create the queue entry with queue_number +1
    last_queue = db.query(Queue).order_by(Queue.position.desc()).first()
    next_queue_number = 1 if not last_queue else last_queue.position + 1

    new_queue_entry = Queue(
        patient_id=quotation_in.patient_id,
        quotation_id=quotation.id,
        type=QueueType.reception,
        position=next_queue_number,
        status="waiting",
        created_at=datetime.now(timezone.utc)
    )
    db.add(new_queue_entry)
    db.commit()
    db.refresh(new_queue_entry)

    return quotation


def get_quotation(db: Session, quotation_id: int):
    return db.query(Quotation).filter(Quotation.id == quotation_id).first()

def get_all_quotations(db: Session, skip: int = 0, limit: int = 100):
    return db.query(Quotation).offset(skip).limit(limit).all()

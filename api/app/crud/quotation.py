from sqlalchemy.orm import Session
from app.models.quotation import Quotation, QuotationItem
from app.models.analysis import AnalysisCatalog
from app.models.agreement import Agreement
from app.schemas.quotation import QuotationCreate
from fastapi import HTTPException

def create_quotation(db: Session, quotation_in: QuotationCreate):
    items_data = []
    total = 0.0

    for item in quotation_in.items:
        analysis = db.query(AnalysisCatalog).filter(AnalysisCatalog.id == item.analysis_id).first()
        if not analysis:
            raise HTTPException(status_code=404, detail=f"Analysis ID {item.analysis_id} not found")
        price = analysis.price
        total += price
        items_data.append(QuotationItem(analysis_id=analysis.id, price=price))

    # Default discount logic
    discount = 0.0
    net_total = total

    # If agreement is selected, apply discount
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
    return quotation

def get_quotation(db: Session, quotation_id: int):
    return db.query(Quotation).filter(Quotation.id == quotation_id).first()

def get_all_quotations(db: Session, skip: int = 0, limit: int = 100):
    return db.query(Quotation).offset(skip).limit(limit).all()

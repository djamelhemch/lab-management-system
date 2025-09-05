from sqlalchemy.orm import Session
from fastapi import HTTPException
from app.models.payment import Payment
from app.models.quotation import Quotation
from app.schemas.payment import PaymentCreate, PaymentUpdate

def create_payment(db: Session, payment_in: PaymentCreate):
    quotation = db.query(Quotation).filter(Quotation.id == payment_in.quotation_id).first()
    if not quotation:
        raise HTTPException(status_code=404, detail="Quotation not found")

    payment = Payment(
        quotation_id=payment_in.quotation_id,
        user_id=payment_in.user_id,
        amount=payment_in.amount,
        method=payment_in.method,
    )
    db.add(payment)
    db.commit()
    db.refresh(payment)
    return payment

def get_payments_for_quotation(db: Session, quotation_id: int):
    return db.query(Payment).filter(Payment.quotation_id == quotation_id).all()

def get_payment(db: Session, payment_id: int):
    return db.query(Payment).filter(Payment.id == payment_id).first()

def update_payment(db: Session, payment_id: int, payment_in: PaymentUpdate):
    payment = get_payment(db, payment_id)
    if not payment:
        raise HTTPException(status_code=404, detail="Payment not found")

    if payment_in.amount is not None:
        payment.amount = payment_in.amount
    if payment_in.method is not None:
        payment.method = payment_in.method

    db.commit()
    db.refresh(payment)
    return payment

def delete_payment(db: Session, payment_id: int):
    payment = get_payment(db, payment_id)
    if not payment:
        raise HTTPException(status_code=404, detail="Payment not found")

    db.delete(payment)
    db.commit()
    return {"detail": "Payment deleted"}

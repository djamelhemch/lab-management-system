from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from typing import List
from app.database import get_db
from app.schemas.payment import PaymentCreate, PaymentUpdate, PaymentSchema
from app.crud import payment as crud_payment

router = APIRouter(prefix="/payments", tags=["Payments"])

@router.post("/", response_model=PaymentSchema)
def create_payment(payment_in: PaymentCreate, db: Session = Depends(get_db)):
    return crud_payment.create_payment(db, payment_in)

@router.get("/quotation/{quotation_id}", response_model=List[PaymentSchema])
def list_payments_for_quotation(quotation_id: int, db: Session = Depends(get_db)):
    return crud_payment.get_payments_for_quotation(db, quotation_id)

@router.get("/{payment_id}", response_model=PaymentSchema)
def read_payment(payment_id: int, db: Session = Depends(get_db)):
    payment = crud_payment.get_payment(db, payment_id)
    if not payment:
        raise HTTPException(status_code=404, detail="Payment not found")
    return payment

@router.put("/{payment_id}", response_model=PaymentSchema)
def update_payment(payment_id: int, payment_in: PaymentUpdate, db: Session = Depends(get_db)):
    return crud_payment.update_payment(db, payment_id, payment_in)

@router.delete("/{payment_id}")
def delete_payment(payment_id: int, db: Session = Depends(get_db)):
    return crud_payment.delete_payment(db, payment_id)

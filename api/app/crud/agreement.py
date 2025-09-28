from sqlalchemy.orm import Session
from app.models.agreement import Agreement
from app.schemas.agreement import AgreementCreate

def create_agreement(db: Session, agreement_data: AgreementCreate):
    agreement = Agreement(**agreement_data.dict())
    db.add(agreement)
    db.commit()
    db.refresh(agreement)
    return agreement

def get_agreement(db: Session, agreement_id: int):
    return db.query(Agreement).filter(Agreement.id == agreement_id).first()

def get_active_agreements(db: Session):
    return db.query(Agreement).filter(Agreement.status == "active").all()

def get_agreements_by_patient(db: Session, patient_id: int):
    return db.query(Agreement).filter(Agreement.patient_id == patient_id).all()

def get_agreements_by_doctor(db: Session, doctor_id: int):
    return db.query(Agreement).filter(Agreement.doctor_id == doctor_id).all()
def get_all_agreements(db: Session):
    """Return all agreements, regardless of status"""
    return db.query(Agreement).all()
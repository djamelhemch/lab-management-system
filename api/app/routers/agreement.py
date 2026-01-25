from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from typing import List, Optional
import logging
from app.database import get_db
import app.crud.agreement as crud
import app.schemas.agreement as schemas

logger = logging.getLogger("agreements")
logging.basicConfig(level=logging.INFO)

router = APIRouter(prefix="/agreements", tags=["Agreements"])

@router.post("/", response_model=schemas.AgreementOut)
def create_agreement(agreement: schemas.AgreementCreate, db: Session = Depends(get_db)):
    
    return crud.create_agreement(db, agreement)

@router.get("/active", response_model=List[schemas.AgreementOut])
def list_active_agreements(db: Session = Depends(get_db)):
    return crud.get_active_agreements(db)

@router.get("/{agreement_id}", response_model=schemas.AgreementOut)
def get_agreement(agreement_id: int, db: Session = Depends(get_db)):
    agreement = crud.get_agreement(db, agreement_id)
    if not agreement:
        raise HTTPException(status_code=404, detail="Agreement not found")
    return agreement

@router.get("/", response_model=List[schemas.AgreementOut])
def list_agreements(status: Optional[str] = None, db: Session = Depends(get_db)):
    if status == "active":
        return crud.get_active_agreements(db)
    return crud.get_all_agreements(db)
@router.put("/{agreement_id}", response_model=schemas.AgreementOut)
def update_agreement(
    agreement_id: int,
    agreement: schemas.AgreementUpdate,
    db: Session = Depends(get_db)
):
    logger.info(f"🔄 UPDATE request for agreement {agreement_id}")
    logger.info(f"📥 Payload received: {agreement.dict()}")

    existing = crud.get_agreement(db, agreement_id)
    if not existing:
        raise HTTPException(status_code=404, detail="Agreement not found")

    update_data = agreement.dict(exclude_unset=True)

    for key, value in update_data.items():
        setattr(existing, key, value)

    db.commit()
    db.refresh(existing)

    logger.info(f"✅ Agreement {agreement_id} updated")
    return existing

    db.commit()
    db.refresh(existing)

    logger.info(f"✅ Agreement {agreement_id} updated successfully")
    return existing
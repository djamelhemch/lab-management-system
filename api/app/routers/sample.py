from fastapi import APIRouter, Depends, HTTPException,Query
from sqlalchemy.orm import Session
from typing import List

from typing import List, Optional
from app.database import get_db
from app.schemas.sample import SampleCreate, SampleRead,SampleStatus
from app.crud import sample as crud_sample

router = APIRouter(prefix="/samples", tags=["Samples"])

@router.get("/", response_model=List[SampleRead])  
def list_samples_route(  
    skip: int = 0,  
    limit: int = 100,  
    q: Optional[str] = Query(None, description="Search by patient, type, barcode"),  
    status: Optional[SampleStatus] = Query(None, description="Filter by status"),  
    db: Session = Depends(get_db)  
):  
    return crud_sample.get_samples(db, skip, limit, q, status)


@router.get("/", response_model=List[SampleRead])
def list_samples_route(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    return crud_sample.get_samples(db, skip, limit)

@router.get("/{sample_id}", response_model=SampleRead)
def get_sample_route(sample_id: int, db: Session = Depends(get_db)):
    db_sample = crud_sample.get_sample(db, sample_id)
    if not db_sample:
        raise HTTPException(status_code=404, detail="Sample not found")
    return db_sample



@router.put("/{sample_id}/status", response_model=SampleRead)
def update_sample_status(
    sample_id: int,
    status: SampleStatus,
    rejection_reason: Optional[str] = None,
    db: Session = Depends(get_db)
):
    """Update sample status with optional rejection reason"""
    db_sample = crud_sample.get_sample(db, sample_id)
    if not db_sample:
        raise HTTPException(status_code=404, detail="Sample not found")

    return crud_sample.update_sample_status(db, sample_id, status, rejection_reason)

@router.get("/status/{status}", response_model=List[SampleRead])
def get_samples_by_status(
    status: SampleStatus,
    db: Session = Depends(get_db)
):
    """Get samples filtered by status for dashboard"""
    return crud_sample.get_samples_by_status(db, status)

@router.get("/{sample_id}/barcode")
def generate_sample_barcode(sample_id: int, db: Session = Depends(get_db)):
    """Generate and return barcode for printing"""
    db_sample = crud_sample.get_sample(db, sample_id)
    if not db_sample:
        raise HTTPException(status_code=404, detail="Sample not found")

    # Return barcode data for printing
    return {
        "barcode": db_sample.barcode,
        "sample_type": db_sample.sample_type,
        "patient_id": db_sample.patient_id,
        "collection_date": db_sample.collection_date
    }

@router.get("/{sample_id}/receipt")
def generate_sample_receipt(sample_id: int, db: Session = Depends(get_db)):
    """Generate receipt with patient details, QR code, etc."""
    # Implementation for receipt generation
    pass
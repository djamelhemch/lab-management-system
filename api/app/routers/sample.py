from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from typing import List

from app.database import get_db
from app.schemas.sample import SampleCreate, SampleRead
from app.crud import sample as crud_sample

router = APIRouter(prefix="/samples", tags=["Samples"])

@router.post("/", response_model=SampleRead)
def create_sample_route(sample: SampleCreate, db: Session = Depends(get_db)):
    return crud_sample.create_sample(db, sample)

@router.get("/", response_model=List[SampleRead])
def list_samples_route(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    return crud_sample.get_samples(db, skip, limit)

@router.get("/{sample_id}", response_model=SampleRead)
def get_sample_route(sample_id: int, db: Session = Depends(get_db)):
    db_sample = crud_sample.get_sample(db, sample_id)
    if not db_sample:
        raise HTTPException(status_code=404, detail="Sample not found")
    return db_sample

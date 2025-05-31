from sqlalchemy.orm import Session
from app.models.sample import Sample  # Correct path
from app.schemas.sample import SampleCreate
from datetime import datetime, timedelta, date

def create_sample(db: Session, sample: SampleCreate) -> Sample:
    db_sample = Sample(**sample.dict())
    db.add(db_sample)
    db.commit()
    db.refresh(db_sample)
    return db_sample

def get_samples(db: Session, skip: int = 0, limit: int = 100):
    return db.query(Sample).offset(skip).limit(limit).all()

def get_sample(db: Session, sample_id: int):
    return db.query(Sample).filter(Sample.id == sample_id).first()

def count_samples_today(db: Session):
    today = date.today()
    start = datetime(today.year, today.month, today.day)
    end = start + timedelta(days=1)
    return db.query(Sample).filter(Sample.collection_date >= start, Sample.collection_date < end).count()

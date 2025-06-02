from sqlalchemy.orm import Session
from app.models.sample import Sample  # Correct path
from app.schemas.sample import SampleCreate,SampleStatus
from datetime import datetime, timedelta, date
from sqlalchemy import or_
import uuid
from datetime import datetime

def generate_barcode(sample_type: str, tube_type: str) -> str:
    """Generate unique barcode based on sample and tube type"""
    timestamp = datetime.now().strftime("%Y%m%d%H%M%S")
    prefix = f"{sample_type[:3].upper()}-{tube_type[:3].upper()}"
    return f"{prefix}-{timestamp}-{str(uuid.uuid4())[:8].upper()}"

def create_sample(db: Session, sample: SampleCreate) -> Sample:
    sample_data = sample.dict()

    # Auto-generate barcode if not provided
    if not sample_data.get('barcode'):
        sample_data['barcode'] = generate_barcode(
            sample_data.get('sample_type', 'UNK'),
            sample_data.get('tube_type', 'STD')
        )

    # Set collection_date if not provided
    if not sample_data.get('collection_date'):
        sample_data['collection_date'] = datetime.now()

    db_sample = Sample(**sample_data)
    db.add(db_sample)
    db.commit()
    db.refresh(db_sample)
    return db_sample

def get_samples(db: Session, skip: int = 0, limit: int = 100, q: str = None, status: SampleStatus = None):
    query = db.query(Sample)

    if q:
        query = query.filter(
            or_(
                Sample.patient_id.like(f"%{q}%"),
                Sample.sample_type.like(f"%{q}%"),
                Sample.barcode.like(f"%{q}%")
            )
        )

    if status:
        query = query.filter(Sample.status == status)

    return query.offset(skip).limit(limit).all()

def get_sample(db: Session, sample_id: int):
    return db.query(Sample).filter(Sample.id == sample_id).first()

def count_samples_today(db: Session):
    today = date.today()
    start = datetime(today.year, today.month, today.day)
    end = start + timedelta(days=1)
    return db.query(Sample).filter(Sample.collection_date >= start, Sample.collection_date < end).count()

def update_sample_status(db: Session, sample_id: int, status: SampleStatus, rejection_reason: str = None):  
    db_sample = db.query(Sample).filter(Sample.id == sample_id).first()  
    if db_sample:  
        db_sample.status = status  
        if status == SampleStatus.rejected and rejection_reason:  
            db_sample.rejection_reason = rejection_reason  
        db.commit()  
        db.refresh(db_sample)  
    return db_sample  

def get_samples_by_status(db: Session, status: SampleStatus):  
    return db.query(Sample).filter(Sample.status == status).all()  

def get_urgent_samples(db: Session):  
    return db.query(Sample).filter(Sample.status == SampleStatus.urgent).all()

def assign_machine_by_tube_type(tube_type: str, centrifugation_time: int = None) -> int:  
    """Assign machine based on tube type and centrifugation requirements"""  
    machine_mapping = {  
        'EDTA': 1,  # Hematology machine  
        'Heparin': 2 if centrifugation_time and centrifugation_time <= 5 else 3,  # Biochemistry (3min) vs Hormonology (25min)  
        'Dry': 2,   # Biochemistry machine  
        'Citrated': 4,  # Coagulation machine  
    }  
    return machine_mapping.get(tube_type, 1)


def update_sample_status(db: Session, sample_id: int, status: SampleStatus, rejection_reason: str = None):
    db_sample = db.query(Sample).filter(Sample.id == sample_id).first()
    if db_sample:
        db_sample.status = status
        if status == SampleStatus.rejected and rejection_reason:
            db_sample.rejection_reason = rejection_reason
        db.commit()
        db.refresh(db_sample)
    return db_sample

def get_samples_by_status(db: Session, status: SampleStatus):
    return db.query(Sample).filter(Sample.status == status).all()

def get_urgent_samples(db: Session):
    return db.query(Sample).filter(Sample.status == SampleStatus.urgent).all()
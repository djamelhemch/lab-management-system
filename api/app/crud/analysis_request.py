from sqlalchemy.orm import Session
from app.models.analysis_request import AnalysisRequest
from app.schemas.analysis_request import AnalysisRequestCreate, AnalysisRequestUpdate

def create_analysis_request(db: Session, data: AnalysisRequestCreate):
    obj = AnalysisRequest(**data.dict())
    db.add(obj)
    db.commit()
    db.refresh(obj)
    return obj


def get_analysis_requests(db: Session, skip=0, limit=100):
    return db.query(AnalysisRequest).offset(skip).limit(limit).all()


def get_analysis_request(db: Session, id: int):
    return db.query(AnalysisRequest).filter(AnalysisRequest.id == id).first()


def update_analysis_request(db: Session, id: int, data: AnalysisRequestUpdate):
    obj = get_analysis_request(db, id)
    if not obj:
        return None
    for key, value in data.dict(exclude_unset=True).items():
        setattr(obj, key, value)
    db.commit()
    db.refresh(obj)
    return obj


def delete_analysis_request(db: Session, id: int):
    obj = get_analysis_request(db, id)
    if not obj:
        return None
    db.delete(obj)
    db.commit()
    return True

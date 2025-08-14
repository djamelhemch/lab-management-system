from sqlalchemy.orm import Session
from app.models.leave_request import LeaveRequest
from app.schemas.leave_request import LeaveRequestCreate, LeaveRequestUpdate

def get_user_leave_requests(db: Session, user_id: int):
    return db.query(LeaveRequest).filter(LeaveRequest.user_id == user_id).all()

def get_leave_request(db: Session, leave_id: int):
    return db.query(LeaveRequest).filter(LeaveRequest.id == leave_id).first()

def create_leave_request(db: Session, user_id: int, leave_in: LeaveRequestCreate):
    leave = LeaveRequest(user_id=user_id, **leave_in.dict())
    db.add(leave)
    db.commit()
    db.refresh(leave)
    return leave

def update_leave_request(db: Session, db_leave: LeaveRequest, updates: LeaveRequestUpdate):
    for field, value in updates.dict(exclude_unset=True).items():
        setattr(db_leave, field, value)
    db.commit()
    db.refresh(db_leave)
    return db_leave

def delete_leave_request(db: Session, db_leave: LeaveRequest):
    db.delete(db_leave)
    db.commit()
    return True

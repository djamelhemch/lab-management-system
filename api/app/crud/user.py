from sqlalchemy.orm import Session
from app.models.user import User, Role, Status
from app.schemas.user import UserCreate
from passlib.hash import bcrypt
from passlib.context import CryptContext
from typing import Optional

pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")

def get_user(db: Session, username: str):
    return db.query(User).filter(User.username == username).first()

def get_all_users(db: Session, role: Optional[str] = None, status: Optional[str] = None):
    query = db.query(User)
    if role:
        query = query.filter(User.role == Role(role))  # convert string to enum member
    if status:
        query = query.filter(User.status == Status(status))
    return query.all()

def add_user(db: Session, user_data: UserCreate):
    hashed_pw = pwd_context.hash(user_data.password)
    db_user = User(
        username=user_data.username,
        full_name=user_data.full_name,
        email=user_data.email,
        role=user_data.role,     # user_data.role is already Role enum
        status=user_data.status, # user_data.status is Status enum
        password_hash=hashed_pw,
    )
    db.add(db_user)
    db.commit()
    db.refresh(db_user)
    return db_user
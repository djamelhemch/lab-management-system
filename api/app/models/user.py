from sqlalchemy import Column, String, Enum, Text, BigInteger, TIMESTAMP
from sqlalchemy.sql import func
from sqlalchemy.orm import relationship
from app.database import Base
from enum import Enum as PyEnum
from sqlalchemy import Enum as SQLEnum
from passlib.context import CryptContext
from app.schemas.user import Role, Status  # ✅ Import shared enums
pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")


class Role(str, PyEnum):
    admin = "admin"
    biologist = "biologist"
    technician = "technician"
    secretary = "secretary"
    intern = "intern"

class Status(str, PyEnum):
    active = "active"
    inactive = "inactive"

class User(Base):
    __tablename__ = "users"

    id = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    username = Column(String(100), unique=True, index=True)
    full_name = Column(String(100))
    email = Column(String(100))
    password_hash = Column(Text)
    role = Column(SQLEnum(Role), nullable=False)
    status = Column(SQLEnum(Status), nullable=False)
    created_at = Column(TIMESTAMP, server_default=func.now())
    # One-to-one relationship with Profile
    profile = relationship(
        "Profile",
        back_populates="user",
        uselist=False,
        cascade="all, delete"
    )
    payments = relationship("Payment", back_populates="user")
    logs = relationship("Log", back_populates="user")
    
    def verify_password(self, plain_password: str) -> bool:
        return pwd_context.verify(plain_password, self.password_hash)
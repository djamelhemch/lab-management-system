from sqlalchemy import Column, BigInteger, Enum, Text, ForeignKey, String
from sqlalchemy.dialects.mysql import ENUM
from sqlalchemy.sql import func
from sqlalchemy.types import TIMESTAMP
from app.database import Base
import enum

class LeaveType(str, enum.Enum):
    leave = "leave"
    certificate = "certificate"
    certificate_of_employment = "certificate_of_employment"

class LeaveStatus(str, enum.Enum):
    pending = "pending"
    approved = "approved"
    rejected = "rejected"

class LeaveRequest(Base):
    __tablename__ = "leave_requests"

    id = Column(BigInteger, primary_key=True, autoincrement=True)
    user_id = Column(BigInteger, ForeignKey("users.id"), nullable=False)
    type = Column(Enum(LeaveType), nullable=False)
    reason = Column(Text, nullable=True)
    status = Column(Enum(LeaveStatus), default=LeaveStatus.pending)
    created_at = Column(TIMESTAMP, server_default=func.current_timestamp())
    updated_at = Column(TIMESTAMP, server_default=func.current_timestamp(), onupdate=func.current_timestamp())

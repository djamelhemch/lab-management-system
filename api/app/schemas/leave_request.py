from pydantic import BaseModel
from typing import Optional
import enum
from datetime import datetime

class LeaveType(str, enum.Enum):
    leave = "leave"
    certificate = "certificate"
    certificate_of_employment = "certificate_of_employment"

class LeaveStatus(str, enum.Enum):
    pending = "pending"
    approved = "approved"
    rejected = "rejected"

class LeaveRequestBase(BaseModel):
    type: LeaveType
    reason: Optional[str] = None
    status: Optional[LeaveStatus] = LeaveStatus.pending

class LeaveRequestCreate(LeaveRequestBase):
    pass

class LeaveRequestUpdate(BaseModel):
    type: Optional[LeaveType] = None
    reason: Optional[str] = None
    status: Optional[LeaveStatus] = None

class LeaveRequestResponse(LeaveRequestBase):
    id: int
    user_id: int
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True

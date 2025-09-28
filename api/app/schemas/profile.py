from pydantic import BaseModel
from typing import Optional, List, Dict, Any
from datetime import datetime

class ProfileBase(BaseModel):
    theme: Optional[str] = "light"
    photo_url: Optional[str] = None
    timetable: Optional[Dict[str, Any]] = None
    checklist: Optional[List[str]] = None
    goals: Optional[List[str]] = None
    self_employed_score: Optional[float] = None
    evaluation_score: Optional[float] = None
    leave_requests: Optional[List[Dict[str, Any]]] = None
    certificates: Optional[List[str]] = None
    employment_certificates: Optional[List[str]] = None

class ProfileCreate(ProfileBase):
    user_id: int

class ProfileUpdate(ProfileBase):
    pass

class ProfileResponse(ProfileBase):
    id: int
    user_id: int
    created_at: datetime
    updated_at: datetime
    email: Optional[str] = None
    class Config:
        orm_mode = True

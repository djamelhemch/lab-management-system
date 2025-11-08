from pydantic import BaseModel, field_validator
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

class ProfileResponse(BaseModel):
    id: int
    user_id: int
    created_at: datetime
    updated_at: datetime
    email: Optional[str] = None
    photo_url: Optional[str] = None  # full URL

    model_config = {
        "from_attributes": True  # important for .from_orm()
    }

    @field_validator("photo_url", mode="before", check_fields=False)
    def build_photo_url(cls, v, info):
        values = info.data
        filename = values.get("photo")
        if filename:
            base_url = "https://lab-management-system-ikt8.onrender.com"
            return f"{base_url}/static/{filename}"
        return None
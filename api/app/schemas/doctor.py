from pydantic import BaseModel
from typing import Optional

class DoctorBase(BaseModel):
    full_name: Optional[str]
    specialty: Optional[str]
    phone: Optional[str]
    email: Optional[str]
    address: Optional[str]
    is_prescriber: Optional[bool]

class DoctorCreate(DoctorBase):
    pass

class DoctorRead(DoctorBase):
    id: int

    class Config:
        orm_mode = True
        json_encoders = {
            str: lambda v: v if v else None,  # Handle empty strings
        }
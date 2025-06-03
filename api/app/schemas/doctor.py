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

class DoctorUpdate(BaseModel):  
    full_name: Optional[str]  
    specialty: Optional[str]  
    phone: Optional[str]  
    email: Optional[str]  
    address: Optional[str]  
    is_prescriber: Optional[bool]
    
class DoctorRead(DoctorBase):
    id: int
    patient_count: int = 0
    
    class Config:
        orm_mode = True
        json_encoders = {
            str: lambda v: v if v else None,  # Handle empty strings
        }
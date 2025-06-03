from pydantic import BaseModel
from typing import Optional
from datetime import date, datetime

class PatientBase(BaseModel):
    file_number: Optional[str] = None
    first_name: Optional[str]
    last_name: Optional[str]
    dob: Optional[date]
    gender: Optional[str]
    address: Optional[str]
    phone: Optional[str]
    email: Optional[str]
    blood_type: Optional[str]
    weight: Optional[float]
    allergies: Optional[str]
    medical_history: Optional[str]
    chronic_conditions: Optional[str]
    doctor_id: Optional[int] = None  # Foreign key to Doctor

class PatientCreate(PatientBase):
    pass

class PatientUpdate(PatientBase):  
    file_number: Optional[str] = None  
    first_name: Optional[str] = None  
    last_name: Optional[str] = None  
    dob: Optional[date] = None  
    gender: Optional[str] = None  
    address: Optional[str] = None  
    phone: Optional[str] = None  
    email: Optional[str] = None  
    blood_type: Optional[str] = None  
    weight: Optional[float] = None  
    allergies: Optional[str] = None  
    medical_history: Optional[str] = None  
    chronic_conditions: Optional[str] = None  
    doctor_id: Optional[int] = None

class PatientRead(BaseModel):
    id: int
    file_number: str
    first_name: str
    last_name: str
    dob: date
    gender: str
    address: str
    phone: str
    email: str
    blood_type: str
    weight: float
    allergies: str
    medical_history: str
    chronic_conditions: str
    created_at: datetime  # Read only
    age: Optional[int] = None
    
    doctor_id: Optional[int] = None
    doctor_name: Optional[str]  = None

class PatientListItem(BaseModel):  
    id: int  
    full_name: str  
    file_number: str
    blood_type: str
    phone: str
    dob: date
    age: Optional[int] = None  # Calculated field, not stored in DB
    
    class Config:
        orm_mode = True
        json_encoders = {
            datetime: lambda v: v.isoformat(),
        }
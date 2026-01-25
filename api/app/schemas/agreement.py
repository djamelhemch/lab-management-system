from pydantic import BaseModel
from typing import Optional, Literal
from datetime import datetime

class AgreementBase(BaseModel):
    patient_id: Optional[int] = None  # Optional field for linking to a patient
    doctor_id: Optional[int] = None  # Optional field for linking to a doctor
    discount_type: Literal["percentage", "fixed"]
    discount_value: float
    status: Literal["active", "inactive"]
    description: Optional[str] = None

class AgreementCreate(AgreementBase):
    pass

class AgreementOut(AgreementBase):
    id: int
    created_at: datetime

    class Config:
        orm_mode = True

class AgreementUpdate(BaseModel):
    patient_id: Optional[int] = None
    doctor_id: Optional[int] = None
    discount_type: Optional[Literal["percentage", "fixed"]] = None
    discount_value: Optional[float] = None
    status: Optional[Literal["active", "inactive"]] = None
    description: Optional[str] = None
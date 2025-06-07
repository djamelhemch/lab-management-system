from pydantic import BaseModel
from typing import List, Optional
from datetime import datetime
import enum
from .quotation_item import QuotationItem, QuotationItemCreate

from pydantic import BaseModel
from typing import List, Optional
from datetime import datetime
import enum

class QuotationStatusEnum(str, enum.Enum):
    draft = 'draft'
    confirmed = 'confirmed'
    converted = 'converted'

class QuotationItemBase(BaseModel):
    analysis_id: int
    price: float

class QuotationItemSchema(QuotationItemBase):
    id: int

    class Config:
        orm_mode = True

class QuotationBase(BaseModel):
    patient_id: int
    status: QuotationStatusEnum
    total: Optional[float] = None
    agreement_id: Optional[int] = None # Optional field for linking to an agreement

class QuotationCreate(QuotationBase):
    items: List[QuotationItemBase]

class QuotationSchema(QuotationBase):
    id: int
    discount_applied: Optional[float] = 0.0  
    net_total: Optional[float] = 0.0         
    created_at: Optional[datetime]
    updated_at: Optional[datetime]
    items: List[QuotationItemSchema]

    class Config:
        orm_mode = True

# Optional: if you want nested patient details
class PatientSummary(BaseModel):
    full_name: str
    file_number: str

class QuotationWithPatientSchema(QuotationSchema):
    patient: PatientSummary
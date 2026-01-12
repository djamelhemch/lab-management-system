from pydantic import BaseModel
from typing import List, Optional
from datetime import datetime
import enum
from .quotation_item import QuotationItem, QuotationItemCreate
from app.schemas.payment import PaymentCreate, PaymentSchema, NestedPaymentCreate
from pydantic import BaseModel, computed_field, Field
from typing import List, Optional
from datetime import datetime
import enum
from app.schemas.analysis import NormalRangeResponse

class AnalysisBase(BaseModel):
    id: int
    code: Optional[str] = None
    name: str
    description: Optional[str] = None
    created_at: Optional[datetime] = None

    class Config:
        orm_mode = True

class UnitResponse(BaseModel):
    id: int
    name: str

    class Config:
        orm_mode = True


class AnalysisWithRangesSchema(BaseModel):
    id: int
    code: Optional[str] = None
    name: str
    unit: Optional[UnitResponse] = None  # <-- add unit here
    normal_ranges: List[NormalRangeResponse] = []

    class Config:
        orm_mode = True
        
class AgreementSummary(BaseModel):
    id: int
    discount_type: Optional[str] = None
    discount_value: Optional[float] = 0.0

    class Config:
        orm_mode = True

class QuotationStatusEnum(str, enum.Enum):
    draft = 'draft'
    confirmed = 'confirmed'
    converted = 'converted'

class QuotationItemBase(BaseModel):
    analysis_id: int
    price: float

class QuotationItemSchema(QuotationItemBase):
    id: int
    analysis_id: int
    price: float
    analysis: Optional[AnalysisWithRangesSchema] = None
    class Config:
        orm_mode = True

class QuotationBase(BaseModel):
    patient_id: int
    status: QuotationStatusEnum
    total: Optional[float] = None
    agreement_id: Optional[int] = None # Optional field for linking to an agreement
    analysis_items: List[QuotationItemCreate]
    payment: Optional[NestedPaymentCreate] = None
    agreement: Optional[AgreementSummary] = None

class QuotationCreate(BaseModel):
    patient_id: int
    status: str 
    agreement_id: Optional[int] = None
    analysis_items: List[QuotationItemBase] = Field(..., alias="items")  
    payment: Optional[NestedPaymentCreate] = None  
    agreement: Optional[AgreementSummary] = None
    
    total: float
    discount_applied: Optional[float] = 0.0
    net_total: float
    outstanding: float
    class Config:
        allow_population_by_field_name = True

class QuotationSchema(QuotationBase):
    id: int
    discount_applied: Optional[float] = 0.0  
    net_total: Optional[float] = 0.0         
    created_at: Optional[datetime]
    updated_at: Optional[datetime]
    analysis_items: List[QuotationItemSchema]
    payments: List[PaymentSchema] = []  
    agreement: Optional[AgreementSummary] = None
    class Config:
        orm_mode = True

# Optional: if you want nested patient details
class PatientSummary(BaseModel):
    first_name: str
    last_name: str
    file_number: str

    @computed_field
    @property
    def full_name(self) -> str:
        return f"{self.first_name} {self.last_name}"

    class Config:
        orm_mode = True

class QuotationWithPatientSchema(BaseModel):
    id: int
    patient_id: int
    status: str
    total: float
    discount_applied: float
    net_total: float
    total_paid: Optional[float] = 0.0
    outstanding: Optional[float] = 0.0
    created_at: datetime
    updated_at: Optional[datetime]
    analysis_items: List[QuotationItemSchema]
    patient: PatientSummary
    payments: List[PaymentSchema] = []
    agreement: Optional[AgreementSummary] = None
    
    class Config:
        orm_mode = True
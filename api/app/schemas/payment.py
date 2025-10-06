from datetime import datetime
from typing import Optional
from pydantic import BaseModel



class PaymentBase(BaseModel):
    amount: float
    method: str
    notes: Optional[str] = None
    amount_received: Optional[float] = None
    change_given: Optional[float] = None
    user_id: int

class UserSummary(BaseModel):
    id: int
    full_name: str

    class Config:
        orm_mode = True

class PaymentCreate(PaymentBase):
    quotation_id: int  # required when calling POST /payments   


class PaymentUpdate(BaseModel):
    amount: Optional[float] = None
    method: Optional[str] = None
    notes: Optional[str] = None
    amount_received: Optional[float] = None
    change_given: Optional[float] = None
    
class NestedPaymentCreate(PaymentBase):
    quotation_id: Optional[int] = None  # ✅ ignored when nested in quotation

class PaymentSchema(PaymentBase):
    id: int
    quotation_id: int
    amount_received: Optional[float] = None
    change_given: Optional[float] = None
    notes: Optional[str] = None
    paid_at: datetime
    user: Optional[UserSummary]  

    class Config:
        orm_mode = True

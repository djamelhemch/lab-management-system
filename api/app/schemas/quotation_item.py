from pydantic import BaseModel
from typing import Optional

class QuotationItemBase(BaseModel):
    analysis_id: int
    price: float

class QuotationItemCreate(QuotationItemBase):
    pass

class QuotationItem(QuotationItemBase):
    id: int
    quotation_id: int

    class Config:
        orm_mode = True

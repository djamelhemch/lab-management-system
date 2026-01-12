from pydantic import BaseModel
from typing import Optional
from datetime import datetime
from app.schemas.analysis import AnalysisResponse
class AnalysisBase(BaseModel):
    id: int
    name: str
    description: Optional[str] = None
    created_at: datetime

    class Config:
        orm_mode = True


class QuotationItemBase(BaseModel):
    analysis_id: int
    price: float


class QuotationItemCreate(QuotationItemBase):
    pass


class QuotationItem(QuotationItemBase):
    id: int
    quotation_id: int
    analysis: Optional[AnalysisResponse] = None  
    
    class Config:
        orm_mode = True


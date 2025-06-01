# schemas/analysis.py
from pydantic import BaseModel, validator
from typing import Optional
from enum import Enum

class SexApplicableEnum(str, Enum):
    M = "M"
    F = "F"
    All = "All"

class AnalysisBase(BaseModel):
    code: Optional[str] = None
    name: str
    category: Optional[str] = None
    unit: Optional[str] = None
    sex_applicable: Optional[SexApplicableEnum] = SexApplicableEnum.All
    age_min: Optional[int] = None
    age_max: Optional[int] = None
    pregnant_applicable: Optional[bool] = False
    sample_type: Optional[str] = None
    normal_min: Optional[float] = None
    normal_max: Optional[float] = None
    formula: Optional[str] = None
    price: Optional[float] = 0.0
    is_active: Optional[bool] = True

class AnalysisCreate(AnalysisBase):
    name: str
    price: float

    @validator('price')
    def price_must_be_positive(cls, v):
        if v < 0:
            raise ValueError('Price must be positive')
        return v

class AnalysisUpdate(AnalysisBase):
    pass

class AnalysisResponse(AnalysisBase):
    id: int

    class Config:
        from_attributes = True
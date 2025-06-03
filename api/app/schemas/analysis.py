from pydantic import BaseModel, validator  , Field
import enum
from typing import Optional  
from datetime import datetime  

class SexApplicableEnum(str, enum.Enum):
    M = "M"
    F = "F"
    All = "All"
    
# Base schemas for lookup tables  
class CategoryAnalyseBase(BaseModel):  
    name: str  
  
class CategoryAnalyseCreate(CategoryAnalyseBase):  
    pass  
  
class CategoryAnalyseResponse(CategoryAnalyseBase):  
    id: int  
    created_at: datetime  
      
    class Config:  
        from_attributes = True  
  
class SampleTypeBase(BaseModel):  
    name: str  
  
class SampleTypeCreate(SampleTypeBase):  
    pass  
  
class SampleTypeResponse(SampleTypeBase):  
    id: int  
    created_at: datetime  
      
    class Config:  
        from_attributes = True  
  
class UnitBase(BaseModel):  
    name: str  
  
class UnitCreate(UnitBase):  
    pass  
  
class UnitResponse(UnitBase):  
    id: int  
    created_at: datetime  
      
    class Config:  
        from_attributes = True  
  
# Updated Analysis schemas  
class AnalysisBase(BaseModel):  
    code: Optional[str] = None  
    name: str  
    category_analyse_id: Optional[int] = None  
    unit_id: Optional[int] = None  
    sex_applicable: Optional[SexApplicableEnum] = SexApplicableEnum.All  
    age_min: Optional[int] = None  
    age_max: Optional[int] = None  
    pregnant_applicable: Optional[bool] = False  
    sample_type_id: Optional[int] = None  
    normal_min: Optional[float] = None  
    normal_max: Optional[float] = None  
    formula: Optional[str] = None  
    price: Optional[float] = 0.0  
  
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
    category_analyse: Optional[CategoryAnalyseResponse] = None  
    unit: Optional[UnitResponse] = None  
    sample_type: Optional[SampleTypeResponse] = None  
  
    class Config:  
        from_attributes = True
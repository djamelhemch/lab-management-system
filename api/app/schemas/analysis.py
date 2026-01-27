from pydantic import BaseModel, validator, Field
import enum
from typing import Optional, List
from datetime import datetime


# ----------------------------
# ENUMS
# ----------------------------
class SexApplicableEnum(str, enum.Enum):
    M = "M"
    F = "F"
    All = "All"


# ----------------------------
# LOOKUP TABLES
# ----------------------------
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


# ----------------------------
# NORMAL RANGE (child table)
# ----------------------------
class NormalRangeBase(BaseModel):
    sex_applicable: Optional[SexApplicableEnum] = "All"
    age_min: Optional[int] = None
    age_max: Optional[int] = None
    pregnant_applicable: Optional[bool] = False
    normal_min: Optional[float] = None
    normal_max: Optional[float] = None

class NormalRangeCreate(NormalRangeBase):
    pass

class NormalRangeUpdate(NormalRangeBase): 
    pass

class NormalRangeResponse(NormalRangeBase):
    id: int

    class Config:
        from_attributes = True


# ----------------------------
# ANALYSIS (parent table)
# ----------------------------
class AnalysisBase(BaseModel):
    code: Optional[str] = None
    name: str
    category_analyse_id: Optional[int] = None
    unit_id: Optional[int] = None
    
    # ❌ DEPRECATED (keep for backward compatibility)
    sample_type_id: Optional[int] = Field(None, exclude=True)
    
    # ✅ NEW multiple sample types
    sample_type_ids: Optional[List[int]] = []
    
    formula: Optional[str] = None
    price: Optional[float] = 0.0
    device_ids: Optional[List[int]] = []
    tube_type: Optional[str] = None 
    is_active: Optional[bool] = True

    
class AnalysisCreate(AnalysisBase):
    name: str
    price: float
    normal_ranges: Optional[List[NormalRangeCreate]] = None

    @validator('price')
    def price_must_be_positive(cls, v):
        if v < 0:
            raise ValueError('Price must be positive')
        return v

class AnalysisUpdate(BaseModel):
    name: Optional[str] = None
    code: Optional[str] = None
    formula: Optional[str] = None
    price: Optional[float] = None
    category_analyse_id: Optional[int] = None
    unit_id: Optional[int] = None
    tube_type: Optional[str] = None
    device_ids: Optional[List[int]] = None
    sample_type_ids: Optional[List[int]] = None
    normal_ranges: Optional[List[NormalRangeCreate]] = None
    is_active: Optional[bool] = None
    user_id: Optional[int] = None

    class Config:
        from_attributes = True


class AnalysisResponse(BaseModel):
    id: int
    code: Optional[str]
    name: str
    price: float
    formula: Optional[str]
    tube_type: Optional[str]
    is_active: bool

    category_analyse: Optional[CategoryAnalyseResponse]
    unit: Optional[UnitResponse]

    # ✅ Only many-to-many output
    sample_types: List[SampleTypeResponse] = []

    # Convenience for frontend
    sample_type_ids: List[int] = Field(default_factory=list)

    normal_ranges: List[NormalRangeResponse] = []

    device_ids: Optional[List[int]] = []
    device_id: Optional[str] = None
    device_names: Optional[List[str]] = []

    @validator("sample_type_ids", always=True)
    def extract_ids(cls, v, values):
        if "sample_types" in values:
            return [st.id for st in values["sample_types"]]
        return []

    @validator("device_ids", pre=True, always=True)
    def parse_device_ids(cls, v):
        if isinstance(v, str):
            return [int(x.strip()) for x in v.split(",") if x.strip()]
        return v or []

    class Config:
        from_attributes = True
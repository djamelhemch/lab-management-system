from pydantic import BaseModel, Field, validator, root_validator, ConfigDict
from typing import Dict, Union
from typing import Optional
from datetime import datetime


class LabResultBase(BaseModel):
    quotation_id: Optional[int] = None
    quotation_item_id: int
    result_value: Optional[str] = None

class LabResultCreate(LabResultBase):
    pass

class LabResultResponse(LabResultBase):
    id: int
    normal_range_id: Optional[int] = None
    interpretation: Optional[str] = None
    status: str
    device_name: Optional[str] = None
    normal_min: Optional[float] = None
    normal_max: Optional[float] = None
    created_at: datetime
    patient_first_name: Optional[str] = None
    patient_last_name: Optional[str] = None
    file_number: Optional[str] = None
    analysis_code: Optional[str] = None
    analysis_name: Optional[str] = None

    model_config = {"from_attributes": True}

class BulkLabResultCreate(BaseModel):
    quotation_id: int
    result_values: Dict[int, Union[float, str]]   
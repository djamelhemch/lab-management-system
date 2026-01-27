from pydantic import BaseModel, Field
from typing import Optional
from datetime import datetime
from enum import Enum
from app.schemas.analysis import SampleTypeResponse

class SampleStatus(str, Enum):
    urgent = "urgent"
    pending = "pending"
    in_progress = "in_progress"
    completed = "completed"
    rejected = "rejected"


class SampleBase(BaseModel):
    patient_id: Optional[int]
    doctor_id: Optional[int]
    
    # ✅ NEW: Link to SampleType catalog
    sample_type_id: Optional[int] = None
    
    # ✅ KEEP: For custom/legacy entries
    sample_type_name: Optional[str] = Field(None, max_length=50)
    
    appearance: Optional[str] = Field(None, max_length=100)
    color: Optional[str] = Field(None, max_length=50)
    odor: Optional[str] = Field(None, max_length=100)
    volume_ml: Optional[float]
    collection_date: Optional[datetime]
    status: Optional[SampleStatus] = SampleStatus.pending
    rejection_reason: Optional[str]
    barcode: Optional[str] = Field(None, max_length=100)
    tube_type: Optional[str] = Field(None, max_length=50)
    assigned_machine_id: Optional[int]


class SampleCreate(SampleBase):
    pass


class SampleRead(SampleBase):
    id: int
    collection_date: Optional[datetime]
    
    # ✅ Include sample type details if linked
    sample_type: Optional[SampleTypeResponse] = None  

    class Config:
        orm_mode = True
        use_enum_values = True


class SampleStatusUpdate(BaseModel):
    status: SampleStatus
    rejection_reason: Optional[str] = None
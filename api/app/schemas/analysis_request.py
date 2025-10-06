from pydantic import BaseModel
from typing import Optional
from enum import Enum

class AnalysisStatus(str, Enum):
    pending = "pending"
    in_progress = "in_progress"
    completed = "completed"
    validated = "validated"
    rejected = "rejected"


class AnalysisRequestBase(BaseModel):
    sample_id: Optional[int] = None
    analysis_id: Optional[int] = None
    device_id: Optional[int] = None
    status: Optional[AnalysisStatus] = AnalysisStatus.pending
    urgent: Optional[bool] = False


class AnalysisRequestCreate(AnalysisRequestBase):
    pass


class AnalysisRequestUpdate(BaseModel):
    status: Optional[AnalysisStatus] = None
    device_id: Optional[int] = None
    urgent: Optional[bool] = None


class AnalysisRequestResponse(AnalysisRequestBase):
    id: int

    class Config:
        orm_mode = True

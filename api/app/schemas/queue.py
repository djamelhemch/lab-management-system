from pydantic import BaseModel, Field, field_validator
from typing import Optional, List
from datetime import datetime
from enum import Enum

class QueueType(str, Enum):
    reception = "reception"
    blood_draw = "blood_draw"

class QueueStatus(str, Enum):
    waiting = "waiting"
    called = "called"
    in_progress = "in_progress"
    completed = "completed"
    no_show = "no_show"

class QueueCreate(BaseModel):
    patient_id: int
    quotation_id: Optional[int] = None
    queue_type: str
    priority: int = Field(default=0, ge=0, le=2)
    notes: Optional[str] = None
    
    @field_validator('queue_type')
    @classmethod
    def validate_queue_type(cls, v):
        if v not in ['reception', 'blood_draw']:
            raise ValueError('queue_type must be "reception" or "blood_draw"')
        return v

class QueueUpdate(BaseModel):
    priority: Optional[int] = Field(None, ge=0, le=2)
    status: Optional[str] = None
    notes: Optional[str] = None
    
    @field_validator('status')
    @classmethod
    def validate_status(cls, v):
        if v is not None and v not in ['waiting', 'called', 'in_progress', 'completed', 'no_show']:
            raise ValueError('Invalid status')
        return v

class QueueOut(BaseModel):
    id: int
    patient_id: int
    quotation_id: Optional[int] = None
    queue_type: str
    position: int
    priority: int
    status: str
    notes: Optional[str] = None
    created_at: datetime
    called_at: Optional[datetime] = None
    started_at: Optional[datetime] = None
    completed_at: Optional[datetime] = None
    estimated_wait_minutes: Optional[int] = None
    patient_name: Optional[str] = None
    ticket_number: int  
    class Config:
        from_attributes = True

class QueuesResponse(BaseModel):
    reception: List[QueueOut]
    blood_draw: List[QueueOut]

class QueueStats(BaseModel):
    total_waiting: int
    total_called: int
    total_in_progress: int
    urgent_count: int
    avg_wait_minutes: float
    estimated_total_wait: int
    current_ticket: Optional[int] = None  
class QueueStatusResponse(BaseModel):
    reception: QueueStats
    blood_draw: QueueStats
    last_updated: datetime
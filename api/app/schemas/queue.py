from pydantic import BaseModel
from typing import Optional, List
from enum import Enum

class QueueType(str, Enum):
    reception = "reception"
    blood_draw = "blood_draw"

class QueueBase(BaseModel):
    patient_id: int
    quotation_id: Optional[int] = None
    type: QueueType

class QueueCreate(QueueBase):
    pass

class QueueOut(QueueBase):
    id: int
    position: int

    class Config:
        orm_mode = True


class QueuesResponse(BaseModel):
    reception: List[QueueOut]
    blood_draw: List[QueueOut]


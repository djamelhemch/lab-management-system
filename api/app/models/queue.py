from sqlalchemy import Column, Integer, String, Enum, ForeignKey,DateTime, func
from sqlalchemy.orm import relationship
from app.database import Base  # your SQLAlchemy Base
import enum
from app.schemas.queue import QueueType, QueueOut

class QueueType(str, enum.Enum):
    reception = "reception"
    blood_draw = "blood_draw"

class Queue(Base):
    __tablename__ = "queues"

    id = Column(Integer, primary_key=True, index=True)
    patient_id = Column(Integer, nullable=False)
    quotation_id = Column(Integer, nullable=True)
    type = Column(Enum(QueueType), nullable=False)
    position = Column(Integer, nullable=False)

    status = Column(String, nullable=False, default="waiting")  # new
    created_at = Column(DateTime(timezone=True), server_default=func.now()) 
    class Config:
        orm_mode = True
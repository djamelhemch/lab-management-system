import enum
from sqlalchemy import Column, Integer, String, DateTime, Enum, ForeignKey, func, BigInteger, TIMESTAMP, Text
from sqlalchemy.orm import relationship
from app.database import Base
from datetime import datetime

class QueueType(str, enum.Enum):
    reception = "reception"
    blood_draw = "blood_draw"

class QueueStatus(str, enum.Enum):
    waiting = "waiting"
    called = "called"
    in_progress = "in_progress"
    completed = "completed"
    no_show = "no_show"

class Queue(Base):
    __tablename__ = "queues"
    
    id = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    patient_id = Column(BigInteger, nullable=False, index=True)
    quotation_id = Column(BigInteger, nullable=True)
    queue_type = Column(String(20), nullable=False, index=True)
    position = Column(Integer, nullable=False)
    priority = Column(Integer, nullable=False, default=0, index=True)
    status = Column(String(20), nullable=False, default='waiting', index=True)
    notes = Column(Text, nullable=True)
    created_at = Column(TIMESTAMP, server_default=func.now(), index=True)
    updated_at = Column(TIMESTAMP, server_default=func.now(), onupdate=func.now())
    called_at = Column(TIMESTAMP, nullable=True)
    started_at = Column(TIMESTAMP, nullable=True)
    completed_at = Column(TIMESTAMP, nullable=True)

class QueueLog(Base):
    __tablename__ = "queue_logs"
    
    id = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    queue_id = Column(BigInteger, nullable=True)
    patient_id = Column(BigInteger, nullable=False, index=True)
    queue_type = Column(String(20), nullable=False, index=True)
    action = Column(String(30), nullable=False, index=True)
    old_status = Column(String(20), nullable=True)
    new_status = Column(String(20), nullable=True)
    position = Column(Integer, nullable=True)
    wait_time_seconds = Column(Integer, nullable=True)
    service_time_seconds = Column(Integer, nullable=True)
    notes = Column(Text, nullable=True)
    created_at = Column(TIMESTAMP, server_default=func.now(), index=True)
    created_by = Column(String(100), nullable=True)
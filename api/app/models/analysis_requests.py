# app/models/analysis_request.py

from sqlalchemy import Column, BigInteger, ForeignKey, Enum, Boolean, DateTime
from sqlalchemy.orm import relationship
from app.database import Base
import enum

class AnalysisStatus(str, enum.Enum):
    pending = "pending"
    in_progress = "in_progress"
    completed = "completed"
    validated = "validated"
    rejected = "rejected"


class AnalysisRequest(Base):
    __tablename__ = "analysis_requests"

    id = Column(BigInteger, primary_key=True, index=True)
    sample_id = Column(BigInteger, ForeignKey("samples.id", ondelete="CASCADE"))
    analysis_id = Column(BigInteger, ForeignKey("analyses.id", ondelete="CASCADE"))
    device_id = Column(BigInteger, ForeignKey("lab_devices.id", ondelete="SET NULL"))
    status = Column(Enum('pending', 'in_progress', 'completed', 'validated', 'rejected', name='analysis_status_enum'), default='pending')
    requested_at = Column(DateTime)
    completed_at = Column(DateTime)

    # ✅ Relationships
    sample = relationship("Sample", back_populates="analysis_requests")

    device = relationship("LabDevice", back_populates="analysis_requests")
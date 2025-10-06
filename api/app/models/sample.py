from sqlalchemy import Column, Integer, String, Float, DateTime, Enum, ForeignKey, BigInteger
from sqlalchemy.orm import relationship
from app.database import Base
import enum

class SampleStatusEnum(str, enum.Enum):
    urgent = 'urgent'
    pending = 'pending'
    in_progress = 'in_progress'
    completed = 'completed'
    rejected = 'rejected'

class Sample(Base):
    __tablename__ = 'samples'

    id = Column(Integer, primary_key=True, index=True)
    patient_id = Column(Integer, ForeignKey('patients.id'))
    doctor_id = Column(Integer, ForeignKey('doctors.id'))
    sample_type = Column(String(50))
    appearance = Column(String(100))
    color = Column(String(50))
    odor = Column(String(100))
    volume_ml = Column(Float)
    collection_date = Column(DateTime)
    status = Column(Enum(SampleStatusEnum), default=SampleStatusEnum.pending)
    rejection_reason = Column(String)
    barcode = Column(String(100), unique=True)
    tube_type = Column(String(50))
    device_id = Column(BigInteger, ForeignKey("lab_devices.id")) 

    # Relationships
    patient = relationship("Patient", back_populates="samples")
    doctor = relationship("Doctor", back_populates="samples")
    
    device = relationship("LabDevice", backref="samples")

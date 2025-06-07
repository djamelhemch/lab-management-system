from sqlalchemy import Column, BigInteger, Enum, ForeignKey, Text, DateTime, DECIMAL
from sqlalchemy.sql import func
from sqlalchemy.orm import relationship
from app.database import Base

class Agreement(Base):
    __tablename__ = "agreements"

    id = Column(BigInteger, primary_key=True, index=True)
    patient_id = Column(BigInteger, ForeignKey("patients.id"), nullable=True)
    doctor_id = Column(BigInteger, ForeignKey("doctors.id"), nullable=True)
    discount_type = Column(Enum("percentage", "fixed", name="discount_type"))
    discount_value = Column(DECIMAL(10, 2))
    status = Column(Enum("active", "inactive", name="discount_status"), default="active")
    description = Column(Text)
    created_at = Column(DateTime, server_default=func.now())

    patient = relationship("Patient", back_populates="agreements")
    doctor = relationship("Doctor", back_populates="agreements")

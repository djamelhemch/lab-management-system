from sqlalchemy import Column, Integer, String,BigInteger, Text, Boolean
from sqlalchemy.orm import relationship
from app.database import Base

class Doctor(Base):
    __tablename__ = "doctors"

    id = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    full_name = Column(String(100), nullable=True)
    specialty = Column(String(100), nullable=True)
    phone = Column(String(20), nullable=True)
    email = Column(String(100), nullable=True)
    address = Column(Text, nullable=True)
    is_prescriber = Column(Boolean, default=False)

    patients = relationship("Patient", back_populates="doctor")
    samples = relationship("Sample", back_populates="doctor")

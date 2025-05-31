from sqlalchemy import Column, BigInteger, String, Date, Enum, Text, Float, DateTime, Integer, ForeignKey
from sqlalchemy.sql import func
from app.database import Base
from sqlalchemy.orm import relationship

class Patient(Base):
    __tablename__ = "patients"

    id = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    file_number = Column(String(20), unique=True, nullable=True)
    first_name = Column(String(100), nullable=True)
    last_name = Column(String(100), nullable=True)
    dob = Column(Date, nullable=True)
    gender = Column(Enum('M', 'F'), nullable=True)
    address = Column(Text, nullable=True)
    phone = Column(String(20), nullable=True)
    email = Column(String(100), nullable=True)
    blood_type = Column(String(5), nullable=True)
    weight = Column(Float, nullable=True)
    allergies = Column(Text, nullable=True)
    medical_history = Column(Text, nullable=True)
    chronic_conditions = Column(Text, nullable=True)
    created_at = Column(DateTime(timezone=True), server_default=func.now())

    # Relationships
    samples = relationship("Sample", back_populates="patient")
    # Add back the doctor_id FK
    doctor_id = Column(Integer, ForeignKey('doctors.id'), nullable=True)

    # Add the relationship
    doctor = relationship("Doctor", back_populates="patients")

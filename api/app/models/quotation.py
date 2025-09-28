from sqlalchemy import Column, Integer, BigInteger, Enum, ForeignKey, DateTime, DECIMAL, Float, String, Numeric
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.database import Base
from app.models.patient import Patient
from pydantic import BaseModel
from typing import List
import enum

# models/quotation.py
from sqlalchemy import Column, Integer, ForeignKey, Float, String, DateTime
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.database import Base

class Quotation(Base):
    __tablename__ = 'quotations'

    id = Column(Integer, primary_key=True, index=True)
    patient_id = Column(Integer, ForeignKey("patients.id"))
    agreement_id = Column(Integer, ForeignKey("agreements.id"), nullable=True)
    status = Column(String, default="draft")
    total = Column(Float)
    discount_applied = Column(Float, default=0.0)
    net_total = Column(Float)

    created_at = Column(DateTime(timezone=True), server_default=func.now())
    updated_at = Column(DateTime(timezone=True), onupdate=func.now())
    outstanding = Column(Numeric(12, 2), default=0.0)
    patient = relationship("Patient", back_populates="quotations")
    analysis_items = relationship(
        "QuotationItem", 
        back_populates="quotation", 
        cascade="all, delete-orphan"
    )
    agreement = relationship("Agreement")
    payments = relationship("Payment", back_populates="quotation", cascade="all, delete-orphan")

class QuotationItem(Base):
    __tablename__ = 'quotation_items'

    id = Column(Integer, primary_key=True, index=True)
    quotation_id = Column(Integer, ForeignKey("quotations.id"))
    analysis_id = Column(Integer, ForeignKey("analysis_catalog.id"))
    price = Column(Float)

    quotation = relationship("Quotation", back_populates="analysis_items")
    analysis = relationship("AnalysisCatalog", back_populates="analysis_items")
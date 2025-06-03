# models/analysis.py
from sqlalchemy import Column, Integer, String, ForeignKey, Float, Boolean, Enum, Text, DateTime
from sqlalchemy.ext.declarative import declarative_base
from app.database import Base
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
import enum

class SexApplicableEnum(str, enum.Enum):
    M = "M"
    F = "F"
    All = "All"

class CategoryAnalyse(Base):
    __tablename__ = "category_analyse"
    
    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(50), unique=True, nullable=False)
    created_at = Column(DateTime(timezone=True), server_default=func.now())
    
    # Relationship
    analyses = relationship("AnalysisCatalog", back_populates="category_analyse")

class SampleType(Base):
    __tablename__ = "sample_types"
    
    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(50), unique=True, nullable=False)
    created_at = Column(DateTime(timezone=True), server_default=func.now())
    
    # Relationship
    analyses = relationship("AnalysisCatalog", back_populates="sample_type")

class Unit(Base):
    __tablename__ = "units"
    
    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(20), unique=True, nullable=False)
    created_at = Column(DateTime(timezone=True), server_default=func.now())
    
    # Relationship
    analyses = relationship("AnalysisCatalog", back_populates="unit")

class AnalysisCatalog(Base):
    __tablename__ = "analysis_catalog"

    id = Column(Integer, primary_key=True, index=True, autoincrement=True)
    code = Column(String(20), unique=True, index=True)
    name = Column(String(100))
    category_analyse_id = Column(Integer, ForeignKey("category_analyse.id"))
    unit_id = Column(Integer, ForeignKey("units.id"))
    sex_applicable = Column(Enum(SexApplicableEnum))
    age_min = Column(Integer)
    age_max = Column(Integer)
    pregnant_applicable = Column(Boolean, default=False)
    sample_type_id = Column(Integer, ForeignKey("sample_types.id"))
    normal_min = Column(Float)
    normal_max = Column(Float)
    formula = Column(Text)
    price = Column(Float, default=0.0)

    # Relationships
    category_analyse = relationship("CategoryAnalyse", back_populates="analyses")
    unit = relationship("Unit", back_populates="analyses")
    sample_type = relationship("SampleType", back_populates="analyses")
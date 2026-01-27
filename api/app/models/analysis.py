# models/analysis.py
from sqlalchemy import Column, Integer, String, ForeignKey, Float, Boolean, Enum, Text, DateTime, Table, BigInteger
from sqlalchemy.ext.declarative import declarative_base
from app.database import Base
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
import enum
#Association table for many-to-many
analysis_sample_types = Table(
    'analysis_sample_types',
    Base.metadata,
    Column('analysis_id', BigInteger, ForeignKey('analysis_catalog.id', ondelete='CASCADE'), primary_key=True),
    Column('sample_type_id', Integer, ForeignKey('sample_types.id', ondelete='CASCADE'), primary_key=True),
    extend_existing=True
)

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

    analyses = relationship(
        "AnalysisCatalog",
        secondary=analysis_sample_types,
        back_populates="sample_types"
    )

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
    formula = Column(Text)
    price = Column(Float, default=0.0)
    tube_type = Column(String(50), nullable=True)
    is_active = Column(Boolean, nullable=False, default=True)
    device_id = Column(Text, nullable=True)

    # Relationships
    category_analyse = relationship("CategoryAnalyse", back_populates="analyses")
    unit = relationship("Unit", back_populates="analyses")
    normal_ranges = relationship("NormalRange", back_populates="analysis", cascade="all, delete-orphan")

    # ✅ ONLY THIS relationship for sample types
    sample_types = relationship(
        "SampleType",
        secondary=analysis_sample_types,
        back_populates="analyses"
    )

    analysis_items = relationship("QuotationItem", back_populates="analysis")
  

class NormalRange(Base):
    __tablename__ = "normal_ranges"

    id = Column(Integer, primary_key=True, index=True)
    analysis_id = Column(Integer, ForeignKey("analysis_catalog.id", ondelete="CASCADE"))
    sex_applicable = Column(Enum(SexApplicableEnum), default=SexApplicableEnum.All)
    age_min = Column(Integer, nullable=True)
    age_max = Column(Integer, nullable=True)
    normal_min = Column(Float, nullable=True)
    normal_max = Column(Float, nullable=True)
    pregnant_applicable = Column(Boolean, default=False)
    # Relationship
    analysis = relationship("AnalysisCatalog", back_populates="normal_ranges")
    lab_results = relationship("LabResult", back_populates="normal_range")
# in AnalysisCatalog

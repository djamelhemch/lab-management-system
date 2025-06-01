# models/analysis.py
from sqlalchemy import Column, Integer, String, Float, Text, Boolean, Enum
from sqlalchemy.ext.declarative import declarative_base
from app.database import Base
import enum

class SexApplicableEnum(str, enum.Enum):
    M = "M"
    F = "F"
    All = "All"

class AnalysisCatalog(Base):  # Rename the class
    __tablename__ = "analysis_catalog"  # Update the table name

    id = Column(Integer, primary_key=True, index=True, autoincrement=True)
    code = Column(String(20), unique=True, index=True)
    name = Column(String(100))
    category = Column(String(50))
    unit = Column(String(20))
    sex_applicable = Column(Enum(SexApplicableEnum))
    age_min = Column(Integer)
    age_max = Column(Integer)
    pregnant_applicable = Column(Boolean, default=False)
    sample_type = Column(String(50))
    normal_min = Column(Float)
    normal_max = Column(Float)
    formula = Column(Text)
    price = Column(Float, default=0.0)
    
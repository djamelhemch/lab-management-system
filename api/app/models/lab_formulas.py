from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy import Column, Integer, String, Text, DateTime
from datetime import datetime
from app.database import Base

class LabFormula(Base):
    __tablename__ = "lab_formulas"

    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(255), nullable=False)
    formula = Column(Text, nullable=False)
    created_by = Column(Integer, nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)
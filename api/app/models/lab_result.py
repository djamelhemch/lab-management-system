from sqlalchemy import Column, BigInteger, ForeignKey, String, Float, Enum, DateTime, Text
from sqlalchemy.orm import relationship
from datetime import datetime
from app.database import Base

class LabResult(Base):
    __tablename__ = "lab_results"

    id = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    quotation_id = Column(BigInteger, ForeignKey("quotations.id"), nullable=False)
    quotation_item_id = Column(BigInteger, ForeignKey("quotation_items.id"), nullable=False)
    normal_range_id = Column(BigInteger, ForeignKey("normal_ranges.id"), nullable=True)

    result_value = Column(String(50), nullable=True)
    interpretation = Column(
        Enum("low", "normal", "high", "critical", "n/a", name="interpretation_enum"),
        default="n/a"
    )
    status = Column(String(50), default="final")
    created_at = Column(DateTime, default=datetime.utcnow)
    device_name = Column(String(255), nullable=True)
    normal_min = Column(Float, nullable=True)
    normal_max = Column(Float, nullable=True)
    # Relationships
    quotation = relationship("Quotation", back_populates="lab_results")
    quotation_item = relationship("QuotationItem", back_populates="lab_results")
    normal_range = relationship("NormalRange", back_populates="lab_results")
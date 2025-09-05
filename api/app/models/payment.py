from sqlalchemy import Column, BigInteger, ForeignKey, String, DECIMAL, TIMESTAMP, text, Text
from sqlalchemy.orm import relationship
from app.database import Base

class Payment(Base):
    __tablename__ = "payments"

    id = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    quotation_id = Column(BigInteger, ForeignKey("quotations.id", ondelete="CASCADE"), nullable=False)
    user_id = Column(BigInteger, ForeignKey("users.id", ondelete="SET NULL"), nullable=True)
    amount = Column(DECIMAL(10, 2), nullable=False)
    method = Column(String(50), nullable=True)
    notes = Column(Text, nullable=True) 
    paid_at = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP"))
    amount_received = Column(DECIMAL(10, 2), nullable=True)
    change_given = Column(DECIMAL(10, 2), nullable=True)
    # Relationships
    quotation = relationship("Quotation", back_populates="payments")
    user = relationship("User", back_populates="payments")

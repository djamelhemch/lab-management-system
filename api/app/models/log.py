from sqlalchemy import Column, BigInteger,Integer, String, Text, TIMESTAMP, func, ForeignKey
from sqlalchemy.orm import relationship
from app.database import Base

class Log(Base):
    __tablename__ = "logs"

    id = Column(BigInteger, primary_key=True, index=True)
    user_id = Column(BigInteger, ForeignKey("users.id", ondelete="SET NULL"), nullable=True)
    action_type = Column(String(100), nullable=False)
    description = Column(Text, nullable=True)
    ip_address = Column(String(45), nullable=True)
    user_agent = Column(Text, nullable=True)
    created_at = Column(TIMESTAMP, server_default=func.now())
    
    user = relationship("User", back_populates="logs")
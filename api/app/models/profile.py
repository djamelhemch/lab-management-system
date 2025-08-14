from sqlalchemy import Column, BigInteger, String, Text, JSON, DECIMAL, ForeignKey, TIMESTAMP, func
from sqlalchemy.orm import relationship
from app.database import Base
from app.models.user import User

class Profile(Base):
    __tablename__ = "profiles"

    id = Column(BigInteger, primary_key=True, autoincrement=True)
    user_id = Column(BigInteger, ForeignKey("users.id", ondelete="CASCADE"), unique=True, nullable=False)
    theme = Column(String(50), default="light")
    photo_url = Column(Text, nullable=True)
    timetable = Column(JSON, nullable=True)
    checklist = Column(JSON, nullable=True)
    goals = Column(JSON, nullable=True)
    self_employed_score = Column(DECIMAL(5,2), nullable=True)
    evaluation_score = Column(DECIMAL(5,2), nullable=True)

    created_at = Column(TIMESTAMP, server_default=func.now())
    updated_at = Column(TIMESTAMP, server_default=func.now(), onupdate=func.now())

    user = relationship("User", back_populates="profile")

from sqlalchemy import Column, Integer, String, ForeignKey, Boolean, DateTime, func
from sqlalchemy.orm import relationship
from app.database import Base

class Setting(Base):
    __tablename__ = "settings"

    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(100), unique=True, nullable=False)

    options = relationship("SettingOption", back_populates="setting", cascade="all, delete-orphan")


class SettingOption(Base):
    __tablename__ = "setting_options"

    id = Column(Integer, primary_key=True, index=True)
    setting_id = Column(Integer, ForeignKey("settings.id", ondelete="CASCADE"), nullable=False)
    value = Column(String(255), nullable=False)
    is_default = Column(Boolean, default=False)
    created_at = Column(DateTime, server_default=func.now())
    updated_at = Column(DateTime, server_default=func.now(), onupdate=func.now())

    setting = relationship("Setting", back_populates="options")
# app/models/ticket_counter.py
from sqlalchemy import Column, Integer, Date, TIMESTAMP
from sqlalchemy.sql import func
from sqlalchemy.ext.declarative import declarative_base

Base = declarative_base()

class TicketCounter(Base):
    __tablename__ = "ticket_counters"
    
    id = Column(Integer, primary_key=True, index=True)
    date = Column(Date, nullable=False, unique=True, index=True)
    reception_next = Column(Integer, default=1, nullable=False)
    blood_draw_next = Column(Integer, default=1, nullable=False)
    created_at = Column(TIMESTAMP, server_default=func.now(), nullable=False)
    updated_at = Column(
        TIMESTAMP, 
        server_default=func.now(), 
        onupdate=func.now(), 
        nullable=False
    )

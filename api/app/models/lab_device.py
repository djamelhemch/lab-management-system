import sqlalchemy
from sqlalchemy import Column, BigInteger, String, Integer
from sqlalchemy.orm import relationship
from app.database import Base  # Your SQLAlchemy Base import

class LabDevice(Base):
    __tablename__ = "lab_devices"

    id = sqlalchemy.Column(sqlalchemy.Integer, primary_key=True, index=True)
    name = sqlalchemy.Column(sqlalchemy.String(100), nullable=False)
    ip_address = sqlalchemy.Column(sqlalchemy.String(45), nullable=False)
    tcp_port = sqlalchemy.Column(sqlalchemy.Integer, nullable=False)
    device_type = sqlalchemy.Column(sqlalchemy.String(50))
    hl7_version = sqlalchemy.Column(sqlalchemy.String(20), default="2.3.1")
    description = sqlalchemy.Column(sqlalchemy.Text)
    created_at = sqlalchemy.Column(sqlalchemy.DateTime, server_default=sqlalchemy.func.now())
    updated_at = sqlalchemy.Column(sqlalchemy.DateTime, server_default=sqlalchemy.func.now(), onupdate=sqlalchemy.func.now())
    
class FhirResource(Base):
    __tablename__ = "fhir_resources"

    id = sqlalchemy.Column(sqlalchemy.Integer, primary_key=True, index=True)
    resource_type = sqlalchemy.Column(sqlalchemy.String(50), index=True)
    resource_json = sqlalchemy.Column(sqlalchemy.JSON)
    device_source = sqlalchemy.Column(sqlalchemy.String(100), nullable=True)  # Track source device 
    created_at = sqlalchemy.Column(sqlalchemy.DateTime, server_default=sqlalchemy.func.now())
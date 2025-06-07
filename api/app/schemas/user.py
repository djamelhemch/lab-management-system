from pydantic import BaseModel, EmailStr, field_serializer
from typing import Optional
from enum import Enum
from datetime import datetime

class Role(str, Enum):
    admin = "admin"
    biologist = "biologist"
    technician = "technician"
    secretary = "secretary"
    intern = "intern"

class Status(str, Enum):
    active = "active"
    inactive = "inactive"

class UserBase(BaseModel):
    username: str
    full_name: Optional[str]
    email: Optional[EmailStr]
    role: Role
    status: Status

class UserCreate(UserBase):
    password: str

class UserOut(BaseModel):
    id: int
    username: str
    full_name: str
    email: str
    role: Role
    status: Status
    created_at: datetime  # let FastAPI serialize this
    
    @property
    def name(self):
        return self.full_name
    class Config:
        orm_mode = True
        from_attributes = True
        use_enum_values = True
from pydantic import BaseModel, EmailStr
from typing import Optional
from enum import Enum

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

class UserOut(UserBase):
    id: int
    created_at: str

    class Config:
        orm_mode = True

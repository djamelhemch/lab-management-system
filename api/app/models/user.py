from sqlalchemy import Column, String, Enum, Text, BigInteger, TIMESTAMP
from sqlalchemy.sql import func
from app.database import Base
import enum
from passlib.context import CryptContext

pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")

class UserRole(enum.Enum):
    admin = "admin"
    biologist = "biologist"
    technician = "technician"
    secretary = "secretary"
    intern = "intern"

class UserStatus(enum.Enum):
    active = "active"
    inactive = "inactive"

class User(Base):
    __tablename__ = "users"

    id = Column(BigInteger, primary_key=True, index=True, autoincrement=True)
    username = Column(String(100), unique=True, index=True)
    full_name = Column(String(100))
    email = Column(String(100))
    password_hash = Column(Text)
    role = Column(Enum(UserRole))
    status = Column(Enum(UserStatus))
    created_at = Column(TIMESTAMP, server_default=func.now())

    def verify_password(self, plain_password: str) -> bool:
        return pwd_context.verify(plain_password, self.password_hash)
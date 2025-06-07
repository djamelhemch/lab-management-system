from app.schemas.user import UserCreate  # your Pydantic model
from app.crud.user import add_user
from app.database import SessionLocal

db = SessionLocal()
user_data = UserCreate(
    username="tekmo",
    full_name="Test User",
    email="tekmo@example.com",
    password="test123",  # plain password
    role="admin",
    status="active"
)
add_user(db, user_data)
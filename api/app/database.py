from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker
import os
from dotenv import load_dotenv

load_dotenv()

DB_URL = (
    f"mysql+pymysql://{os.getenv('DB_USER')}:{os.getenv('DB_PASSWORD')}"
    f"@{os.getenv('DB_HOST')}:{os.getenv('DB_PORT')}/{os.getenv('DB_NAME')}"
)

print("Connecting to: database")

# --- FIX: prevent "Lost connection" errors ---
engine = create_engine(
    DB_URL,
    pool_pre_ping=True,      # Detect dead/stale connections automatically
    pool_recycle=280,        # Recycle connections before MySQL terminates them
    pool_timeout=30,         # Prevent long waits on a dead socket
    pool_size=10,            # Safe default for Render
    max_overflow=20          # Allow bursts under load
)

SessionLocal = sessionmaker(
    autocommit=False,
    autoflush=False,
    bind=engine
)

Base = declarative_base()


def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()


# Optional console output
try:
    with engine.connect() as conn:
        print("Database connection established")
except Exception as e:
    print("Failed to connect to the database:", e)
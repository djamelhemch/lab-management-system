from pydantic import BaseModel
from datetime import datetime
from typing import Optional

class LogSchema(BaseModel):
    id: int
    user_id: int
    user: Optional[dict] = None  # include full_name if joined
    action_type: str
    description: Optional[str] = None
    ip_address: Optional[str] = None
    user_agent: Optional[str] = None
    created_at: datetime

    class Config:
        orm_mode = True
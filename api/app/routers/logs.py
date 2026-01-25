from fastapi import APIRouter, Depends, Query
from sqlalchemy.orm import Session
from typing import Optional
from app.database import get_db
from app.models.log import Log  # your SQLAlchemy Log model
from fastapi import Request
router = APIRouter()

@router.get("/logs")
def get_logs(
    db: Session = Depends(get_db),
    user_id: Optional[int] = Query(None),
    action_type: Optional[str] = Query(None),
    page: int = Query(1, ge=1),
    per_page: int = Query(20, ge=1, le=100)
):
    """
    Fetch logs with optional filters and pagination:
    - user_id
    - action_type
    - page
    - per_page
    """
    query = db.query(Log)

    if user_id:
        query = query.filter(Log.user_id == user_id)
    if action_type:
        query = query.filter(Log.action_type == action_type)

    total = query.count()
    logs = query.order_by(Log.created_at.desc()).offset((page - 1) * per_page).limit(per_page).all()

    result = []
    for log in logs:
        result.append({
            "id": log.id,
            "user_id": log.user_id,
            "user_name": log.user.full_name if log.user else None,
            "action_type": log.action_type,
            "description": log.description,
            "ip_address": log.ip_address,
            "user_agent": log.user_agent,
            "created_at": log.created_at.strftime("%Y-%m-%d %H:%M:%S")
        })

    return {
        "data": result,
        "pagination": {
            "page": page,
            "per_page": per_page,
            "total": total,
            "last_page": (total + per_page - 1) // per_page
        }
    }
@router.get("/debug-headers")
def debug_headers(request: Request):
    return {
        "client.host": request.client.host,
        "cf-connecting-ip": request.headers.get("cf-connecting-ip"),
        "x-forwarded-for": request.headers.get("x-forwarded-for"),
        "x-real-ip": request.headers.get("x-real-ip"),
        "user-agent": request.headers.get("user-agent")
    }
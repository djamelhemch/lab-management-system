from sqlalchemy.orm import Session
from fastapi import Request
from app.models.log import Log
from functools import wraps
from datetime import datetime
import inspect
from sqlalchemy import text
from typing import Optional

def log_action(
    db: Session,
    user_id: int,
    action_type: str,
    description: str = None,
    request: Request = None
):
    ip = request.client.host if request else None
    ua = request.headers.get("user-agent") if request else None

    log_entry = Log(
        user_id=user_id,
        action_type=action_type,
        description=description,
        ip_address=ip,
        user_agent=ua
    )
    db.add(log_entry)
    db.commit()
    
def log_route(action_type: str):
    def decorator(func):
        @wraps(func)
        async def wrapper(*args, **kwargs):
            request: Request = kwargs.get("request")
            db: Session = kwargs.get("db")
            current_user = kwargs.get("current_user")

            try:
                result = await func(*args, **kwargs) if inspect.iscoroutinefunction(func) else func(*args, **kwargs)
            except Exception as e:
                # Log failed attempt
                if db and current_user:
                    _insert_log(
                        db, 
                        current_user.id, 
                        f"{action_type}_failed", 
                        f"{action_type} failed: {str(e)}", 
                        request  # ✅ Pass request
                    )
                raise

            # ✅ Build description...
            description = action_type
            if result and hasattr(result, "id"):
                description += f" (ID: {result.id})"

            if db and current_user:
                _insert_log(db, current_user.id, action_type, description, request)  # ✅ Pass request

            return result
        return wrapper
    return decorator


def get_real_client_ip(request: Request) -> str:
    """
    Get real client IP through Cloudflare/Proxy headers
    Priority order: CF-Connecting-IP > X-Forwarded-For > X-Real-IP > request.client.host
    """
    if request is None:
        return "unknown"
    
    # ✅ Cloudflare specific
    cf_connecting_ip = request.headers.get("cf-connecting-ip")
    if cf_connecting_ip:
        return cf_connecting_ip
    
    # ✅ Standard proxy headers
    x_forwarded_for = request.headers.get("x-forwarded-for")
    if x_forwarded_for:
        # Take first IP (real client, not proxies)
        return x_forwarded_for.split(",")[0].strip()
    
    x_real_ip = request.headers.get("x-real-ip")
    if x_real_ip:
        return x_real_ip
    
    # Fallback
    return request.client.host

def get_real_user_agent(request: Request) -> str:
    """Get real User-Agent, fallback to generic desktop/mobile detection"""
    if request is None:
        return "Unknown"
    
    ua = request.headers.get("user-agent", "")
    if not ua:
        return "Unknown"
    
    ua_lower = ua.lower()
    
    # ✅ Detect device type from UA
    if "mobile" in ua_lower or "android" in ua_lower or "iphone" in ua_lower or "ipad" in ua_lower:
        return "Mobile Browser"
    elif "chrome" in ua_lower:
        return "Chrome Desktop"
    elif "firefox" in ua_lower:
        return "Firefox Desktop"
    elif "safari" in ua_lower:
        return "Safari Desktop"
    else:
        return ua[:100] + "..."  # Truncate long UAs
async def _handle_log(func, args, kwargs, action_type, is_async: bool):
    request: Request = kwargs.get("request", None)
    db: Session = kwargs.get("db", None)
    current_user = kwargs.get("current_user", None)

    # Call the route function
    try:
        if is_async:
            result = await func(*args, **kwargs)
        else:
            result = func(*args, **kwargs)
    except Exception as e:
        # Log failure
        if db and current_user:
            _insert_log(db, current_user.id, action_type, f"{action_type} failed: {str(e)}", request)
        raise

    # Build description
    description = action_type

    entity_keys = {
        "quotation_id": "Quotation ID",
        "patient_id": "Patient ID",
        "user_id": "User ID",
        "analysis_id": "Analysis ID",
    }
    for key, label in entity_keys.items():
        val = kwargs.get(key) or getattr(result, "id", None)
        if val:
            description += f" ({label}: {val})"
            break

    patient_name = None
    if hasattr(result, "patient") and hasattr(result.patient, "full_name"):
        patient_name = result.patient.full_name
    elif isinstance(result, dict):
        patient_name = result.get("patient", {}).get("full_name")
    if patient_name:
        description += f" (Patient: {patient_name})"

    if db and current_user:
        _insert_log(db, current_user.id, action_type, description, request)

    return result

def _insert_log(db: Session, user_id: Optional[int], action_type: str, description: str, request: Request = None):
    ip_address = get_real_client_ip(request)
    user_agent = get_real_user_agent(request)

    db.execute(
        text(
            "INSERT INTO logs (user_id, action_type, description, ip_address, user_agent, created_at) "
            "VALUES (:user_id, :action_type, :description, :ip_address, :user_agent, :created_at)"
        ),
        {
            "user_id": user_id,
            "action_type": action_type,
            "description": description,
            "ip_address": ip_address,
            "user_agent": user_agent,
            "created_at": datetime.utcnow()
        }
    )
    db.commit()
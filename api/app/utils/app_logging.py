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
                if db:
                    user_identifier = getattr(current_user, "id", None) or kwargs.get("form_data", {}).get("username")
                    _insert_log(
                        db, 
                        user_identifier, 
                        action_type, 
                        f"{action_type} failed: {str(e)}", 
                        request
                    )
                raise

            # Build a detailed description
            description = action_type

            # Try to enhance description based on returned object
            if result:
                # Patient
                if hasattr(result, "file_number") and hasattr(result, "first_name") and hasattr(result, "last_name"):
                    description += f" (file_number: {result.file_number}, name: {result.first_name} {result.last_name})"

                # Quotation
                elif hasattr(result, "id") and hasattr(result, "patient"):
                    patient = getattr(result, "patient")
                    if hasattr(patient, "full_name"):
                        description += f" (Quotation ID: {result.id}, Patient: {patient.full_name})"

                # Analysis
                elif hasattr(result, "id") and hasattr(result, "name"):
                    description += f" (Analysis ID: {result.id}, name: {result.name})"

            # Fallback username info
            if kwargs.get("form_data") and hasattr(kwargs["form_data"], "username"):
                description += f" (username: {kwargs['form_data'].username})"

            # Determine user_id
            user_id = getattr(current_user, "id", None) or 0

            if db:
                _insert_log(db, user_id, action_type, description, request)

            return result

        return wrapper
    return decorator


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
    ip_address = request.client.host if request else None
    user_agent = request.headers.get("user-agent") if request else None

    db.execute(
        text(
            "INSERT INTO logs (user_id, action_type, description, ip_address, user_agent, created_at) "
            "VALUES (:user_id, :action_type, :description, :ip_address, :user_agent, :created_at)"
        ),
        {
            "user_id": user_id,  # This can now be None and insert as NULL in DB
            "action_type": action_type,
            "description": description,
            "ip_address": ip_address,
            "user_agent": user_agent,
            "created_at": datetime.utcnow()
        }
    )
    db.commit()
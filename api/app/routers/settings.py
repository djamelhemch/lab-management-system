from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from app.database import get_db
import app.crud.settings as crud
import app.schemas.settings as schemas
from app.routers.auth import get_current_user
from app.models.user import User
from fastapi import HTTPException
from typing import List

router = APIRouter(prefix="/settings", tags=["Settings"])

@router.post("/", response_model=schemas.SettingSchema)
def create_setting(setting: schemas.SettingCreate, db: Session = Depends(get_db)):
    return crud.create_setting(db, setting)

@router.get("/", response_model=list[schemas.SettingSchema])
def list_settings(db: Session = Depends(get_db)):
    return crud.get_settings(db)

@router.get("/{setting_id}", response_model=schemas.SettingSchema)
def get_setting(setting_id: int, db: Session = Depends(get_db)):
    setting = crud.get_setting(db, setting_id)
    if not setting:
        raise HTTPException(status_code=404, detail="Setting not found")
    return setting

@router.post("/{setting_id}/options", response_model=schemas.SettingOptionSchema)
def add_option(setting_id: int, option: schemas.SettingOptionCreate, db: Session = Depends(get_db)):
    return crud.add_option(db, setting_id, option)

@router.get("/default/{setting_name}", response_model=schemas.SettingOptionSchema)
def get_default(setting_name: str, db: Session = Depends(get_db)):
    default = crud.get_default_option(db, setting_name)
    if not default:
        raise HTTPException(status_code=404, detail="Default option not found")
    return default

@router.put("/{id}/options/{option_id}/default", response_model=schemas.SettingOptionSchema)
def set_default_option(id: int, option_id: int, db: Session = Depends(get_db)):
    return crud.set_default_option(db, id, option_id)

@router.put("/{setting_id}/options/{option_id}", response_model=schemas.SettingOptionSchema)
def update_option(
    setting_id: int, 
    option_id: int, 
    option_update: schemas.SettingOptionUpdate, 
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    """Update a setting option (admin only)"""
    if current_user.role != "admin":
        raise HTTPException(status_code=403, detail="Only admins can update settings")
    
    updated_option = crud.update_option(db, setting_id, option_id, option_update)
    if not updated_option:
        raise HTTPException(status_code=404, detail="Option not found")
    return updated_option

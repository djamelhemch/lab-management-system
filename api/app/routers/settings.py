from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from app.database import get_db
import app.crud.settings as crud
import app.schemas.settings as schemas


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
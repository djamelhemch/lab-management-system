from sqlalchemy.orm import Session
from fastapi import HTTPException
import app.models.settings as models
import app.schemas.settings as schemas
# Create a setting
def create_setting(db: Session, setting: schemas.SettingCreate):
    db_setting = models.Setting(name=setting.name)
    db.add(db_setting)
    db.commit()
    db.refresh(db_setting)
    return db_setting
    
def set_default_option(db: Session, setting_id: int, option_id: int):
    setting = db.query(models.Setting).filter(models.Setting.id == setting_id).first()
    if not setting:
        raise HTTPException(status_code=404, detail="Setting not found")

    option = db.query(models.SettingOption).filter(
        models.SettingOption.id == option_id,
        models.SettingOption.setting_id == setting_id
    ).first()

    if not option:
        raise HTTPException(status_code=404, detail="Option not found")

    # Unset all other defaults
    db.query(models.SettingOption).filter(
        models.SettingOption.setting_id == setting_id
    ).update({models.SettingOption.is_default: False})

    # Set this one as default
    option.is_default = True
    db.commit()
    db.refresh(option)
    return option
# Get all settings
def get_settings(db: Session):
    return db.query(models.Setting).all()

# Get one setting
def get_setting(db: Session, setting_id: int):
    return db.query(models.Setting).filter(models.Setting.id == setting_id).first()

# Add an option
def add_option(db: Session, setting_id: int, option: schemas.SettingOptionCreate):
    setting = get_setting(db, setting_id)
    if not setting:
        raise HTTPException(status_code=404, detail="Setting not found")

    if option.is_default:
        db.query(models.SettingOption).filter(
            models.SettingOption.setting_id == setting_id
        ).update({models.SettingOption.is_default: False})

    new_option = models.SettingOption(
        setting_id=setting_id,
        value=option.value,
        is_default=option.is_default
    )
    db.add(new_option)
    db.commit()
    db.refresh(new_option)
    return new_option

# Get current default value
def get_default_option(db: Session, setting_name: str):
    setting = db.query(models.Setting).filter(models.Setting.name == setting_name).first()
    if not setting:
        return None
    return db.query(models.SettingOption).filter(
        models.SettingOption.setting_id == setting.id,
        models.SettingOption.is_default == True
    ).first()

def update_option(db: Session, setting_id: int, option_id: int, option_update: schemas.SettingOptionUpdate):
    """Update a setting option"""
    option = db.query(models.SettingOption).filter(
        models.SettingOption.id == option_id,
        models.SettingOption.setting_id == setting_id
    ).first()
    
    if option:
        option.value = option_update.value
        db.commit()
        db.refresh(option)
        return option
    return None
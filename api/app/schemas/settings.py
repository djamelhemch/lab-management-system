from pydantic import BaseModel
from typing import List, Optional

class SettingOptionBase(BaseModel):
    value: str
    is_default: Optional[bool] = False

class SettingOptionCreate(SettingOptionBase):
    pass

class SettingOptionSchema(SettingOptionBase):
    id: int
    class Config:
        orm_mode = True

class SettingBase(BaseModel):
    name: str

class SettingCreate(SettingBase):
    pass

class SettingSchema(SettingBase):
    id: int
    options: List[SettingOptionSchema] = []
    class Config:
        orm_mode = True

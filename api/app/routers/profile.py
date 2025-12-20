import os
import shutil
from fastapi import APIRouter, Depends, HTTPException, UploadFile, File, Request
from fastapi.staticfiles import StaticFiles
from types import SimpleNamespace
from app.schemas.profile import ProfileUpdate

from sqlalchemy.orm import Session
from app.schemas.profile import ProfileResponse, ProfileCreate, ProfileUpdate
from app.crud import profile as crud_profile
from app.database import get_db
from app.routers.auth import get_current_user  # Add this import (adjust path if needed)
from app.models.profile import Profile  # Add this import (adjust path if needed)
from app.models.user import User  # Add this import (adjust path if needed)
from app.utils.app_logging import log_route
import logging

router = APIRouter(prefix="/profiles", tags=["Profiles"])

# Directory for uploaded photos
UPLOAD_DIR = "uploads/profile_photos"
os.makedirs(UPLOAD_DIR, exist_ok=True)

# file_path is constructed per-upload inside the upload_profile_photo endpoint,
# so the top-level filename variable is not defined and this line is removed.


@router.get("/{user_id}", response_model=ProfileResponse)
def read_profile(
    user_id: int,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
    request: Request = None
):
    profile = crud_profile.get_profile(db, user_id)
    if not profile:
        raise HTTPException(status_code=404, detail="Profile not found")
    
    return ProfileResponse.model_validate(profile)


@router.post("/", response_model=ProfileResponse)
def create_profile(profile_in: ProfileCreate, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    profile = crud_profile.create_profile(db, profile_in)
    return ProfileResponse.model_validate(profile)

@router.put("/{user_id}", response_model=ProfileResponse)
def update_profile_endpoint(user_id: int, updates: ProfileUpdate, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    updated_profile = crud_profile.update_profile(db, user_id, updates)
    return ProfileResponse.model_validate(updated_profile)


@router.post("/photo")
async def upload_profile_photo(
    file: UploadFile = File(...),
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
    request: Request = None
):
    if not file:
        raise HTTPException(status_code=400, detail="No file uploaded")

    ext = os.path.splitext(file.filename)[1]
    filename = f"user_{current_user.id}{ext}"
    file_path = os.path.join(UPLOAD_DIR, filename)

    with open(file_path, "wb") as f:
        shutil.copyfileobj(file.file, f)

    # IMPORTANT: store only filename in DB
    profile = crud_profile.update_profile(
        db,
        current_user.id,
        ProfileUpdate(photo_url=filename)  # Store only filename
    )
    # Return only filename, not full URL
    return {"photo_url": filename}












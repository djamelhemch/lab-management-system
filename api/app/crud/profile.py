from sqlalchemy.orm import Session
from app.models.profile import Profile
from app.schemas.profile import ProfileCreate, ProfileUpdate

def get_profile(db: Session, user_id: int):
    return db.query(Profile).filter(Profile.user_id == user_id).first()

def create_profile(db: Session, profile: ProfileCreate):
    # Only pass fields that exist in the Profile model
    allowed_fields = {c.name for c in Profile.__table__.columns}
    profile_data = {k: v for k, v in profile.dict().items() if k in allowed_fields}

    db_profile = Profile(**profile_data)
    db.add(db_profile)
    db.commit()
    db.refresh(db_profile)
    return db_profile

def update_profile(db: Session, user_id: int, updates: ProfileUpdate):
    db_profile = get_profile(db, user_id)
    allowed_fields = {c.name for c in Profile.__table__.columns}

    if not db_profile:
        # Profile doesn't exist → create it safely
        profile_data = {k: v for k, v in updates.dict(exclude_unset=True).items() if k in allowed_fields}
        profile_data['user_id'] = user_id
        db_profile = Profile(**profile_data)
        db.add(db_profile)
    else:
        # Profile exists → update allowed fields
        for key, value in updates.dict(exclude_unset=True).items():
            if key in allowed_fields:
                setattr(db_profile, key, value)

    db.commit()
    db.refresh(db_profile)
    return db_profile
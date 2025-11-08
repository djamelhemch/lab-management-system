from sqlalchemy.orm import Session
from app.models.profile import Profile
from app.schemas.profile import ProfileCreate, ProfileUpdate
from app.models.user import User
def get_profile(db: Session, user_id: int):
    return db.query(Profile).filter(Profile.user_id == user_id).first()

def create_profile(db: Session, profile: ProfileCreate):
    db_profile = Profile(**profile.dict(exclude_unset=True))
    db.add(db_profile)
    db.commit()
    db.refresh(db_profile)
    return db_profile

def update_profile(db: Session, user_id: int, updates: ProfileUpdate):
    db_profile = get_profile(db, user_id)
    
    if not db_profile:
        # Profile doesn't exist → create it
        profile_data = updates.dict(exclude_unset=True)
        profile_data['user_id'] = user_id
        db_profile = Profile(**profile_data)
        db.add(db_profile)
    else:
        # Update only fields that exist on the model
        for key, value in updates.dict(exclude_unset=True).items():
            if hasattr(db_profile, key):
                setattr(db_profile, key, value)

    db.commit()
    db.refresh(db_profile)
    return db_profile
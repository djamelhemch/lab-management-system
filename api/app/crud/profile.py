from sqlalchemy.orm import Session
from app.models.profile import Profile
from app.schemas.profile import ProfileCreate, ProfileUpdate
from app.models.user import User
def get_profile(db: Session, user_id: int) -> Profile | None:
    """
    Returns the Profile ORM object for a given user_id, or None if not found.
    Joins with User to fetch email and attaches it as an attribute.
    """
    profile = db.query(Profile).filter(Profile.user_id == user_id).first()
    if profile:
        # Attach email dynamically from the User table
        user = db.query(User).filter(User.id == profile.user_id).first()
        profile.email = user.email if user else None
    return profile


def create_profile(db: Session, profile_in: ProfileCreate) -> Profile:
    """
    Creates a Profile ORM object.
    Only passes fields that exist in the Profile model.
    """
    allowed_fields = {c.name for c in Profile.__table__.columns}
    profile_data = {k: v for k, v in profile_in.model_dump().items() if k in allowed_fields}

    db_profile = Profile(**profile_data)
    db.add(db_profile)
    db.commit()
    db.refresh(db_profile)
    return db_profile


def update_profile(db: Session, user_id: int, updates: ProfileUpdate) -> Profile:
    """
    Updates an existing Profile ORM object, or creates one if it doesn't exist.
    """
    db_profile = get_profile(db, user_id)
    allowed_fields = {c.name for c in Profile.__table__.columns}

    update_data = updates.model_dump(exclude_unset=True)

    if not db_profile:
        # Profile doesn't exist → create it safely
        update_data["user_id"] = user_id
        db_profile = Profile(**{k: v for k, v in update_data.items() if k in allowed_fields})
        db.add(db_profile)
    else:
        # Update only allowed fields
        for key, value in update_data.items():
            if key in allowed_fields:
                setattr(db_profile, key, value)

    db.commit()
    db.refresh(db_profile)
    return db_profile
    return db_profile
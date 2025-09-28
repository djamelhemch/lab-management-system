# auth.py
from fastapi import FastAPI, Depends, HTTPException, status
from fastapi.security import OAuth2PasswordBearer, OAuth2PasswordRequestForm
from jose import JWTError, jwt
from datetime import datetime, timedelta
from typing import Optional
from app.crud.user import get_user, get_all_users, add_user
from app.models.user import User
from app.models.user_session import UserSession  # Ensure this path is correct
from app.schemas.user import UserCreate, UserOut
from fastapi import APIRouter
from sqlalchemy.orm import Session
from app.database import get_db
from fastapi import Request
from app.utils.logging import _insert_log,log_route    # Ensure this path is correct
import logging
logger = logging.getLogger("uvicorn.error")

SECRET_KEY = "test"
ALGORITHM = "HS256"
ACCESS_TOKEN_EXPIRE_MINUTES = 30

oauth2_scheme = OAuth2PasswordBearer(tokenUrl="token")


router = APIRouter(prefix="", tags=["Auth"])


def authenticate_user(db: Session, username: str, password: str):
    user = db.query(User).filter(User.username == username).first()
    if not user or not user.verify_password(password):
        return None
    return user

def create_access_token(data: dict, expires_delta: Optional[timedelta] = None):
    to_encode = data.copy()
    if expires_delta:
        expire = datetime.utcnow() + expires_delta
    else:
        expire = datetime.utcnow() + timedelta(minutes=15)
    to_encode.update({"exp": expire})
    encoded_jwt = jwt.encode(to_encode, SECRET_KEY, algorithm=ALGORITHM)
    return encoded_jwt

def insert_active_session(db: Session, user_id: int, token: str, created_at: datetime, expires_at: datetime):
    session_record = UserSession(
        user_id=user_id,
        token=token,
        created_at=created_at,
        expires_at=expires_at,
        is_connected=True 
    )
    db.add(session_record)
    db.commit()

def delete_active_session(db: Session, token: str):
    session_record = db.query(UserSession).filter(UserSession.token == token).first()
    if session_record:
        # Mark session as disconnected instead of deleting
        session_record.is_connected = False
        db.commit()

def fetch_active_user_ids(db: Session) -> list[int]:
    now = datetime.utcnow()
    # Only fetch sessions that are connected and unexpired
    active_sessions = db.query(UserSession).filter(
        UserSession.expires_at > now,
        UserSession.is_connected == True
    ).all()
    return list(set(session.user_id for session in active_sessions))

async def get_current_user(
    token: str = Depends(oauth2_scheme), 
    db: Session = Depends(get_db)
):
    credentials_exception = HTTPException(
        status_code=status.HTTP_401_UNAUTHORIZED,
        detail="Could not validate credentials",
        headers={"WWW-Authenticate": "Bearer"},
    )
    try:
        payload = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
        username: str = payload.get("sub")
        role: str = payload.get("role")  # ✅ Extract role
        if username is None or role is None:
            raise credentials_exception
    except JWTError:
        raise credentials_exception

    user = get_user(db, username)
    if user is None:
        raise credentials_exception

    user.role = role  # ✅ Assign role from token
    return user

# Add these routes to your FastAPI app
@router.post("/token")
async def login(
    request: Request,
    form_data: OAuth2PasswordRequestForm = Depends(),
    db: Session = Depends(get_db)
):
    logger.info(f"Login attempt for username: {form_data.username} from {request.client.host}")
    try:
        user = authenticate_user(db, form_data.username, form_data.password)
        if not user:
            logger.warning(f"Failed login attempt: username={form_data.username} from ip={request.client.host}")
            _insert_log(
                db,
                user_id=None,
                action_type="login",
                description=f"Failed login attempt (username: {form_data.username})",
                request=request
            )
            raise HTTPException(status_code=401, detail="Incorrect username or password")

        logger.info(f"User {user.username} authenticated successfully")
        _insert_log(db, user_id=user.id, action_type="login", description="Successful login", request=request)

        # Create token
        access_token_expires = timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
        access_token = create_access_token(
            data={"sub": user.username, "role": user.role.value},
            expires_delta=access_token_expires
        )

        # Save active session info
        logger.debug(f"Inserting active session for user_id={user.id}")
        insert_active_session(db, user.id, access_token, datetime.utcnow(), expires_at=datetime.utcnow() + access_token_expires)

        logger.info(f"Login successful for user: {user.username}")
        return {"access_token": access_token, "token_type": "bearer"}

    except HTTPException as auth_exc:
        logger.error(f"Authentication HTTPException: {auth_exc.detail} for username={form_data.username}")
        raise auth_exc  # Already logged above if failed auth
    except Exception as e:
        logger.exception(f"Unexpected error during login for username={form_data.username}: {str(e)}")
        _insert_log(
            db,
            user_id=None,
            action_type="login",
            description=f"Login error: {str(e)} (username: {form_data.username})",
            request=request
        )
        raise HTTPException(status_code=500, detail="Internal server error")
    
@router.get("/users/me", response_model=UserOut)
async def read_users_me(current_user: User = Depends(get_current_user)):
    return current_user

@router.get("/users/online-status")
async def online_status(db: Session = Depends(get_db)):
    now = datetime.utcnow()
    # Query user sessions where session is active and not expired
    active_sessions = db.query(UserSession).filter(
        UserSession.is_connected == True,
        UserSession.expires_at > now
    ).all()

    # Collect unique user IDs from active sessions
    online_user_ids = list({session.user_id for session in active_sessions})
    return {"online_user_ids": online_user_ids}

# User management endpoints
@router.get("/users", response_model=list[UserOut])
async def get_users(
    role: Optional[str] = None,
    status: Optional[str] = None,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    try:
        users = get_all_users(db, role, status)
        return users
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@router.post("/logout")
async def logout(token: str = Depends(oauth2_scheme), db: Session = Depends(get_db)):
    # Remove the session for this token from DB/Cache
    delete_active_session(db, token)
    return {"message": "Logged out successfully"}

@router.post("/users")
@log_route("create_user")
async def create_user(
    user: UserCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    request: Request = None
):
    if current_user.role != "admin":
        raise HTTPException(status_code=403, detail="Only admins can create users")
    
    # Check if username exists
    if get_user(db, user.username):
        raise HTTPException(status_code=400, detail="Username already exists")
    
    # Check if email exists
    if db.query(User).filter(User.email == user.email).first():
        raise HTTPException(status_code=400, detail="Email already exists")

    return add_user(db, user)

@router.get("/users/{user_id}")
def read_user(user_id: int, db: Session = Depends(get_db)):
    user = db.query(User).filter(User.id == user_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="User not found")
    return user

@router.put("/users/{user_id}")
@log_route("update_user")
def update_user(user_id: int, user_data: UserCreate, db: Session = Depends(get_db), current_user: User = Depends(get_current_user), request: Request = None):
    db_user = db.query(User).filter(User.id == user_id).first()
    if not db_user:
        raise HTTPException(status_code=404, detail="User not found")
    
    for field, value in user_data.dict(exclude_unset=True).items():
        setattr(db_user, field, value)

    db.commit()
    db.refresh(db_user)

    return db_user

@router.delete("/users/{user_id}")
@log_route("delete_user")
def delete_user(user_id: int, db: Session = Depends(get_db), current_user: User = Depends(get_current_user), request: Request = None):
    user = db.query(User).filter(User.id == user_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="User not found")
    if current_user.role != "admin":
        raise HTTPException(status_code=403, detail="Only admins can delete users")
    db.delete(user)
    db.commit()
    return {"detail": "User deleted successfully"}

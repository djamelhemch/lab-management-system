# auth.py
from fastapi import FastAPI, Depends, HTTPException, status
from fastapi.security import OAuth2PasswordBearer, OAuth2PasswordRequestForm
from jose import JWTError, jwt
from datetime import datetime, timedelta
from typing import Optional
from app.crud.user import get_user, get_all_users, add_user
from app.models.user import User
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
    """
    Authenticate user and return access token.
    Logs every attempt (success or failure).
    """
    try:
        user = authenticate_user(db, form_data.username, form_data.password)
        if not user:
            # Log failed login
            _insert_log(
                db,
                user_id=0,
                action_type="login",
                description=f"Login failed (username: {form_data.username})",
                request=request
            )
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Incorrect username or password",
                headers={"WWW-Authenticate": "Bearer"},
            )

        # Log successful login
        _insert_log(
            db,
            user_id=user.id,
            action_type="login",
            description=f"Login success (username: {user.username})",
            request=request
        )

        # Generate token
        access_token_expires = timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
        access_token = create_access_token(
            data={"sub": user.username, "role": user.role.value},
            expires_delta=access_token_expires
        )

        return {"access_token": access_token, "token_type": "bearer"}

    except HTTPException as auth_exc:
        raise auth_exc  # Already logged above if failed auth
    except Exception as e:
        # Log unexpected error
        _insert_log(
            db,
            user_id=0,
            action_type="login",
            description=f"Login error: {str(e)} (username: {form_data.username})",
            request=request
        )
        raise HTTPException(status_code=500, detail="Internal server error")
    
@router.get("/users/me", response_model=UserOut)
async def read_users_me(current_user: User = Depends(get_current_user)):
    return current_user

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

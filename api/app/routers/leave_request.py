from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from app.database import get_db
from app.schemas.leave_request import LeaveRequestCreate, LeaveRequestUpdate, LeaveRequestResponse
from app.crud import leave_request as crud_leave
from app.routers.auth import get_current_user  # Adjust import path if needed

router = APIRouter(prefix="/leave-requests", tags=["Leave Requests"])

@router.get("/", response_model=list[LeaveRequestResponse])
def list_my_leave_requests(
    db: Session = Depends(get_db),
    current_user = Depends(get_current_user)
):
    return crud_leave.get_user_leave_requests(db, current_user.id)

@router.post("/", response_model=LeaveRequestResponse)
def create_my_leave_request(
    leave_in: LeaveRequestCreate,
    db: Session = Depends(get_db),
    current_user = Depends(get_current_user)
):
    return crud_leave.create_leave_request(db, current_user.id, leave_in)

@router.put("/{leave_id}", response_model=LeaveRequestResponse)
def update_my_leave_request(
    leave_id: int,
    updates: LeaveRequestUpdate,
    db: Session = Depends(get_db),
    current_user = Depends(get_current_user)
):
    leave = crud_leave.get_leave_request(db, leave_id)
    if not leave or leave.user_id != current_user.id:
        raise HTTPException(status_code=404, detail="Leave request not found")
    return crud_leave.update_leave_request(db, leave, updates)

@router.delete("/{leave_id}")
def delete_my_leave_request(
    leave_id: int,
    db: Session = Depends(get_db),
    current_user = Depends(get_current_user)
):
    leave = crud_leave.get_leave_request(db, leave_id)
    if not leave or leave.user_id != current_user.id:
        raise HTTPException(status_code=404, detail="Leave request not found")
    crud_leave.delete_leave_request(db, leave)
    return {"message": "Deleted successfully"}

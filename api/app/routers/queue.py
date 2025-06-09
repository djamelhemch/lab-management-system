from fastapi import FastAPI, HTTPException, Depends, APIRouter
from fastapi.responses import JSONResponse
from sqlalchemy.orm import Session
from app.database import SessionLocal, engine
from app.models.queue import Queue, QueueType
from app.schemas.queue import QueueCreate, QueueOut, QueuesResponse
from typing import List

router = APIRouter(prefix="/queues", tags=["Queue"])

# Dependency to get DB session
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

# Helper to reorder positions for a queue type
def reorder_positions(db: Session, queue_type: QueueType):
    queues = db.query(Queue).filter(Queue.type == queue_type).order_by(Queue.position).all()
    for i, q in enumerate(queues, 1):
        q.position = i
    db.commit()


@router.get("/", response_model=QueuesResponse)
def get_queues(db: Session = Depends(get_db)):
    reception = db.query(Queue).filter(Queue.type == QueueType.reception).order_by(Queue.position).all()
    blood_draw = db.query(Queue).filter(Queue.type == QueueType.blood_draw).order_by(Queue.position).all()
    return {
        "reception": reception,
        "blood_draw": blood_draw
    }

@router.post("/", response_model=QueueOut, status_code=201)
def add_to_queue(queue_in: QueueCreate, db: Session = Depends(get_db)):
    max_position = db.query(Queue).filter(Queue.type == queue_in.type).order_by(Queue.position.desc()).first()
    pos = max_position.position + 1 if max_position else 1

    queue = Queue(
        patient_id=queue_in.patient_id,
        quotation_id=queue_in.quotation_id,
        type=queue_in.type,
        position=pos
    )
    db.add(queue)
    db.commit()
    db.refresh(queue)
    return queue

@router.delete("/{queue_id}", status_code=204)
def remove_from_queue(queue_id: int, db: Session = Depends(get_db)):
    queue = db.query(Queue).filter(Queue.id == queue_id).first()
    if not queue:
        raise HTTPException(status_code=404, detail="Queue item not found")

    queue_type = queue.type
    db.delete(queue)
    db.commit()
    reorder_positions(db, queue_type)
    return

@router.post("/move-next", response_model=QueueOut)
def move_next_to_blood_draw(db: Session = Depends(get_db)):  
    # Remove the first patient in blood_draw queue (if any)  
    first_blood_draw = db.query(Queue).filter(Queue.type == QueueType.blood_draw).order_by(Queue.position).first()  
    if first_blood_draw:  
        db.delete(first_blood_draw)  
        db.commit()  
        reorder_positions(db, QueueType.blood_draw)  
  
    # Get the next patient in reception queue  
    next_reception = db.query(Queue).filter(Queue.type == QueueType.reception).order_by(Queue.position).first()  
    if not next_reception:  
        raise HTTPException(status_code=404, detail="Reception queue empty")  
  
    # Remove from reception  
    db.delete(next_reception)  
    db.commit()  
    reorder_positions(db, QueueType.reception)  
  
    # Add to blood_draw queue with next position  
    max_position = db.query(Queue).filter(Queue.type == QueueType.blood_draw).order_by(Queue.position.desc()).first()  
    pos = max_position.position + 1 if max_position else 1  
  
    new_blood_draw = Queue(  
        patient_id=next_reception.patient_id,  
        quotation_id=next_reception.quotation_id,  
        type=QueueType.blood_draw,  
        position=pos,  
    )  
    db.add(new_blood_draw)  
    db.commit()  
    db.refresh(new_blood_draw)  
  
    reorder_positions(db, QueueType.blood_draw)  
  
    return new_blood_draw

@router.get("/status")
def queue_status(db: Session = Depends(get_db)):
    def summarize_queue(queue_type: QueueType):
        queue_items = db.query(Queue).filter(Queue.type == queue_type).order_by(Queue.position).all()
        total = len(queue_items)
        current = queue_items[0].position if total > 0 else None
        next_ = queue_items[1].position if total > 1 else None
        
        # For example, assume avg_wait_time in minutes, you can improve this logic
        avg_wait_time = 7  
        estimated_wait_time = avg_wait_time * total
        
        return {
            "current": current,
            "next": next_,
            "total": total,
            "avg_wait_time": avg_wait_time,
            "estimated_wait_time": estimated_wait_time,
        }

    reception_summary = summarize_queue(QueueType.reception)
    blood_draw_summary = summarize_queue(QueueType.blood_draw)

    return JSONResponse({
        "reception": reception_summary,
        "blood_draw": blood_draw_summary,
    })
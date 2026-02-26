from fastapi import APIRouter, Depends, HTTPException, Query, Request
from fastapi.responses import StreamingResponse
from sqlalchemy.orm import Session, joinedload
from sqlalchemy import func, and_, or_,select
from datetime import datetime, timedelta, timezone
from app.database import get_db
from app.models.queue import Queue, QueueLog, QueueType, QueueStatus
from app.models.patient import Patient
from app.models.quotation import Quotation
from app.schemas.queue import QueueCreate, QueueOut, QueuesResponse, QueueStats, QueueStatusResponse
from typing import List, Optional, AsyncGenerator
import statistics
import json
import asyncio
from datetime import date  
from app.models.ticket_counter import TicketCounter  # create this model
router = APIRouter(prefix="/queues", tags=["queues"])

def get_queue_stats(queue_type: str, db: Session) -> dict:
    """Shared stats logic for /status and /status/stream"""
    waiting = (
        db.query(func.count(Queue.id))
        .filter(Queue.queue_type == queue_type, Queue.status == "waiting")
        .scalar()
    )

    called = (
        db.query(func.count(Queue.id))
        .filter(Queue.queue_type == queue_type, Queue.status == "called")
        .scalar()
    )

    in_progress = (
        db.query(func.count(Queue.id))
        .filter(Queue.queue_type == queue_type, Queue.status == "in_progress")
        .scalar()
    )

    urgent = (
        db.query(func.count(Queue.id))
        .filter(
            Queue.queue_type == queue_type,
            Queue.status.in_(["waiting", "called"]),
            Queue.priority > 0,
        )
        .scalar()
    )

    avg_service = get_avg_service_time(db, queue_type)
    avg_wait_min = avg_service // 60
    estimated_total = (waiting * avg_service) // 60

    # ✅ current ticket: lowest ticket_number among waiting/called
    current_ticket = (
        db.query(func.min(Queue.ticket_number))
        .filter(
            Queue.queue_type == queue_type,
            Queue.status.in_(["waiting", "called"]),
        )
        .scalar()
    )

    return {
        "total_waiting": waiting,
        "total_called": called,
        "total_in_progress": in_progress,
        "urgent_count": urgent,
        "avg_wait_minutes": avg_wait_min,
        "estimated_total_wait": estimated_total,
        "current_ticket": current_ticket,  # ✅ this goes to SSE/JS
    }

def utc_now():
    """Get current UTC timestamp"""
    return datetime.now(timezone.utc).replace(tzinfo=None)

def calculate_time_diff(start, end):
    """Calculate time difference in seconds"""
    if not start or not end:
        return None
    if start.tzinfo:
        start = start.replace(tzinfo=None)
    if end.tzinfo:
        end = end.replace(tzinfo=None)
    return int((end - start).total_seconds())

def log_action(db: Session, queue_id: Optional[int], patient_id: int, queue_type: str, 
               action: str, old_status: str = None, new_status: str = None, 
               position: int = None, notes: str = None):
    """Log queue action"""
    log = QueueLog(
        queue_id=queue_id,
        patient_id=patient_id,
        queue_type=queue_type,
        action=action,
        old_status=old_status,
        new_status=new_status,
        position=position,
        notes=notes
    )
    db.add(log)
    db.commit()

def reorder_queue(db: Session, queue_type: str):
    """Reorder positions based on priority"""
    items = db.query(Queue).filter(
        Queue.queue_type == queue_type,
        Queue.status == 'waiting'
    ).order_by(Queue.priority.desc(), Queue.created_at).all()
    
    for idx, item in enumerate(items, 1):
        item.position = idx
    db.commit()

def get_avg_service_time(db: Session, queue_type: str) -> int:
    """Get average service time from last 50 completed"""
    week_ago = utc_now() - timedelta(days=7)
    
    logs = db.query(QueueLog).filter(
        QueueLog.queue_type == queue_type,
        QueueLog.action == 'completed',
        QueueLog.service_time_seconds.isnot(None),
        QueueLog.created_at >= week_ago
    ).order_by(QueueLog.created_at.desc()).limit(50).all()
    
    if not logs:
        return 300  # 5 minutes default
    
    avg = statistics.mean([log.service_time_seconds for log in logs])
    return int(avg)

def enrich_queue_item(db: Session, item: Queue, avg_service_time: int):
    """Add calculated fields to queue item"""
    # Get patient name
    patient = db.query(Patient).filter(Patient.id == item.patient_id).first()
    item.patient_name = patient.full_name if patient else f"Patient #{item.patient_id}"
    
    # Calculate estimated wait
    if item.status == 'waiting':
        item.estimated_wait_minutes = (item.position * avg_service_time) // 60
    else:
        item.estimated_wait_minutes = 0

# ==================== ENDPOINTS ====================

@router.get("/", response_model=QueuesResponse)
def get_all_queues(db: Session = Depends(get_db)):
    """Get all active queues"""
    
    reception_items = db.query(Queue).filter(
        Queue.queue_type == 'reception',
        Queue.status.in_(['waiting', 'called'])
    ).order_by(Queue.priority.desc(), Queue.position).all()
    
    blood_draw_items = db.query(Queue).filter(
        Queue.queue_type == 'blood_draw',
        Queue.status.in_(['waiting', 'called', 'in_progress'])
    ).order_by(Queue.priority.desc(), Queue.position).all()
    
    # Enrich with patient names and estimates
    reception_avg = get_avg_service_time(db, 'reception')
    blood_draw_avg = get_avg_service_time(db, 'blood_draw')
    
    for item in reception_items:
        enrich_queue_item(db, item, reception_avg)
    
    for item in blood_draw_items:
        enrich_queue_item(db, item, blood_draw_avg)
    
    return QueuesResponse(
        reception=[QueueOut.from_orm(item) for item in reception_items],
        blood_draw=[QueueOut.from_orm(item) for item in blood_draw_items]
    )

@router.post("/", response_model=QueueOut, status_code=201)
def add_to_queue(data: QueueCreate, db: Session = Depends(get_db)):
    """Add patient to queue"""
    
    # Validation unchanged...
    patient = db.query(Patient).filter(Patient.id == data.patient_id).first()
    if not patient:
        raise HTTPException(404, "Patient not found")
    
    existing = db.query(Queue).filter(
        Queue.patient_id == data.patient_id,
        Queue.queue_type == data.queue_type,
        Queue.status.in_(['waiting', 'called', 'in_progress'])
    ).first()
    
    if existing:
        raise HTTPException(400, f"Patient already in {data.queue_type} queue")
    
    # ✅ Daily ticket using utc_now()
    today = utc_now().date()  # ✅ Uses your timezone-safe function!
    
    counter = db.execute(
        select(TicketCounter)
        .where(TicketCounter.date == today)
        .with_for_update()
    ).scalar_one_or_none()
    
    if not counter:
        counter = TicketCounter(date=today)
        db.add(counter)
        db.flush()
    
    # Assign ticket
    if data.queue_type == 'reception':
        ticket_number = counter.reception_next
        counter.reception_next += 1
    else:
        ticket_number = counter.blood_draw_next
        counter.blood_draw_next += 1
    
    # Save counter
    db.commit()
    
    # Relative position
    max_pos = db.query(func.max(Queue.position)).filter(
        Queue.queue_type == data.queue_type,
        Queue.status == 'waiting'
    ).scalar() or 0
    
    # Create queue item
    queue_item = Queue(
        patient_id=data.patient_id,
        quotation_id=data.quotation_id,
        queue_type=data.queue_type,
        position=max_pos + 1,
        ticket_number=ticket_number,  # ✅ Global daily!
        priority=data.priority,
        status='waiting',
        notes=data.notes
    )
    db.add(queue_item)
    db.commit()
    db.refresh(queue_item)
    
    # Rest unchanged (log, reorder, enrich...)
    log_action(db, queue_item.id, data.patient_id, data.queue_type, 'added', 
               new_status='waiting', position=queue_item.position)
    reorder_queue(db, data.queue_type)
    db.refresh(queue_item)
    avg_time = get_avg_service_time(db, data.queue_type)
    enrich_queue_item(db, queue_item, avg_time)
    
    return QueueOut.from_orm(queue_item)
@router.post("/move-next", response_model=QueueOut)
def move_to_blood_draw(db: Session = Depends(get_db)):
    """Move next reception patient to blood draw"""
    
    # Complete current blood draw patient
    current = db.query(Queue).filter(
        Queue.queue_type == 'blood_draw',
        Queue.status.in_(['called', 'in_progress'])
    ).first()
    
    if current:
        now = utc_now()
        current.completed_at = now
        current.status = 'completed'
        
        # Calculate times
        wait_time = calculate_time_diff(current.created_at, current.called_at or now)
        service_time = calculate_time_diff(current.started_at or current.called_at, now)
        
        # Log completion
        log_entry = QueueLog(
            queue_id=current.id,
            patient_id=current.patient_id,
            queue_type='blood_draw',
            action='completed',
            old_status='in_progress',
            new_status='completed',
            wait_time_seconds=wait_time,
            service_time_seconds=service_time
        )
        db.add(log_entry)
        db.delete(current)
        db.commit()
    
    # Get next from reception
    next_patient = db.query(Queue).filter(
        Queue.queue_type == 'reception',
        Queue.status == 'waiting'
    ).order_by(Queue.priority.desc(), Queue.position).first()
    
    if not next_patient:
        raise HTTPException(404, "No patients in reception queue")
    
    # ✅ COPY ticket_number from reception!
    patient_id = next_patient.patient_id
    quotation_id = next_patient.quotation_id
    priority = next_patient.priority
    ticket_number = next_patient.ticket_number  # ✅ Reuse same ticket!
    
    # Log reception completion
    now = utc_now()
    wait_time = calculate_time_diff(next_patient.created_at, now)
    log_entry = QueueLog(
        queue_id=next_patient.id,
        patient_id=patient_id,
        queue_type='reception',
        action='completed',
        old_status='waiting',
        new_status='completed',
        wait_time_seconds=wait_time
    )
    db.add(log_entry)
    
    db.delete(next_patient)
    db.commit()
    
    # Create in blood_draw WITH ticket_number
    new_item = Queue(
        patient_id=patient_id,
        quotation_id=quotation_id,
        queue_type='blood_draw',
        position=1,
        ticket_number=ticket_number,  # ✅ Same ticket moves!
        priority=priority,
        status='called',
        called_at=now
    )
    db.add(new_item)
    db.commit()
    db.refresh(new_item)
    
    # Log call
    log_action(db, new_item.id, patient_id, 'blood_draw', 'called', 
               old_status=None, new_status='called', position=1)
    
    # Reorder queues
    reorder_queue(db, 'reception')
    reorder_queue(db, 'blood_draw')
    db.refresh(new_item)
    
    # Enrich response
    avg_time = get_avg_service_time(db, 'blood_draw')
    enrich_queue_item(db, new_item, avg_time)
    
    return QueueOut.from_orm(new_item)

@router.put("/{queue_id}/priority")
def update_priority(queue_id: int, priority: int = Query(..., ge=0, le=2), 
                   db: Session = Depends(get_db)):
    """Update queue item priority"""
    
    item = db.query(Queue).filter(Queue.id == queue_id).first()
    if not item:
        raise HTTPException(404, "Queue item not found")
    
    old_priority = item.priority
    old_position = item.position
    item.priority = priority
    db.commit()
    
    # Reorder
    reorder_queue(db, item.queue_type)
    db.refresh(item)
    
    # Log
    log_action(db, queue_id, item.patient_id, item.queue_type, 'priority_changed',
               notes=f"Priority changed from {old_priority} to {priority}")
    
    return {"success": True, "old_position": old_position, "new_position": item.position}

@router.delete("/{queue_id}", status_code=204)
def remove_from_queue(queue_id: int, reason: str = Query("manual_removal"), 
                     db: Session = Depends(get_db)):
    """Remove patient from queue"""
    
    item = db.query(Queue).filter(Queue.id == queue_id).first()
    if not item:
        raise HTTPException(404, "Queue item not found")
    
    # Log removal
    log_action(db, queue_id, item.patient_id, item.queue_type, 'removed',
               old_status=item.status, notes=reason)
    
    queue_type = item.queue_type
    db.delete(item)
    db.commit()
    
    # Reorder
    reorder_queue(db, queue_type)

@router.get("/status", response_model=QueueStatusResponse)
def get_queue_status(db: Session = Depends(get_db)):
    """Get queue statistics"""
    return QueueStatusResponse(
        reception=QueueStats(**get_queue_stats('reception', db)),  # ✅ Reuse
        blood_draw=QueueStats(**get_queue_stats('blood_draw', db)),  # ✅ Reuse
        last_updated=utc_now()
    )

@router.get("/status/stream")
async def stream_queue_status(request: Request):
    async def event_generator() -> AsyncGenerator[str, None]:
        while True:
            # ✅ NEW DB SESSION EVERY ITERATION
            db = next(get_db())  # Fresh session
            
            try:
                reception_ticket = db.query(func.min(Queue.ticket_number)).filter(
                    Queue.queue_type == 'reception', 
                    Queue.status.in_(['waiting', 'called'])
                ).scalar()
                
                blood_draw_ticket = db.query(func.min(Queue.ticket_number)).filter(
                    Queue.queue_type == 'blood_draw', 
                    Queue.status.in_(['waiting', 'called', 'in_progress'])
                ).scalar()
                
                data = {
                    "reception": {"current_ticket": reception_ticket},
                    "blood_draw": {"current_ticket": blood_draw_ticket},
                    "timestamp": utc_now().isoformat()
                }
                
                yield f"data: {json.dumps(data)}\n\n"
                db.close()  # Explicit close
                
            except Exception as e:
                print(f"SSE Error: {e}")
                if 'db' in locals():
                    db.close()
            
            await asyncio.sleep(2)  # Slower polling
    
    return StreamingResponse(
        event_generator(), 
        media_type="text/event-stream",
        headers={
            "Cache-Control": "no-cache",
            "Connection": "keep-alive",
            "Access-Control-Allow-Origin": "*",
        }
    )
@router.get("/counters")
def get_ticket_counters(db: Session = Depends(get_db)):
    """Get current ticket counter values"""
    today = utc_now().date()
    counter = db.query(TicketCounter).filter(TicketCounter.date == today).first()
    
    return {
        "reception_next": counter.reception_next if counter else 1,
        "blood_draw_next": counter.blood_draw_next if counter else 1
    }
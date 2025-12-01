from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from sqlalchemy import func
from datetime import datetime, timedelta, date
from app.database import get_db
from app.models.quotation import Quotation
from app.models.payment import Payment
from app.models.patient import Patient
from app.models.sample import Sample
import statistics

router = APIRouter(prefix="/analytics", tags=["analytics"])

@router.get("/predictions")
def get_predictive_analytics(db: Session = Depends(get_db)):
    """Generate predictive analytics and forecasts"""
    
    # Get historical data (last 90 days)
    ninety_days_ago = datetime.now() - timedelta(days=90)
    
    # Revenue trend (last 12 weeks)
    weekly_revenue = []
    for i in range(12):
        week_start = datetime.now() - timedelta(weeks=i+1)
        week_end = datetime.now() - timedelta(weeks=i)
        
        revenue = db.query(func.coalesce(func.sum(Payment.amount), 0)).filter(
            Payment.paid_at >= week_start,
            Payment.paid_at < week_end
        ).scalar()
        
        weekly_revenue.append(float(revenue))
    
    weekly_revenue.reverse()
    
    # Predict next week revenue (simple moving average)
    if len(weekly_revenue) >= 4:
        predicted_next_week = statistics.mean(weekly_revenue[-4:])
    else:
        predicted_next_week = statistics.mean(weekly_revenue) if weekly_revenue else 0
    
    # Patient growth trend
    monthly_patients = []
    for i in range(6):
        month_start = datetime.now() - timedelta(days=30*(i+1))
        month_end = datetime.now() - timedelta(days=30*i)
        
        count = db.query(func.count(Patient.id)).filter(
            Patient.created_at >= month_start,
            Patient.created_at < month_end
        ).scalar()
        
        monthly_patients.append(count)
    
    monthly_patients.reverse()
    
    # Predict next month patients
    if len(monthly_patients) >= 3:
        predicted_patients = round(statistics.mean(monthly_patients[-3:]))
    else:
        predicted_patients = round(statistics.mean(monthly_patients)) if monthly_patients else 0
    
    # Sample volume trend
    daily_samples = []
    for i in range(30):
        day = date.today() - timedelta(days=i)
        count = db.query(func.count(Sample.id)).filter(
            func.date(Sample.collection_date) == day
        ).scalar()
        daily_samples.append(count)
    
    avg_daily_samples = statistics.mean(daily_samples) if daily_samples else 0
    
    # Collection rate (conversion rate from quotation to payment)
    total_quotations = db.query(func.count(Quotation.id)).filter(
        Quotation.status == "converted",
        Quotation.created_at >= ninety_days_ago
    ).scalar()
    
    paid_quotations = db.query(func.count(Payment.id.distinct())).filter(
        Payment.paid_at >= ninety_days_ago
    ).scalar()
    
    collection_rate = (paid_quotations / total_quotations * 100) if total_quotations > 0 else 0
    
    # Revenue at risk (outstanding > 30 days)
    thirty_days_ago = datetime.now() - timedelta(days=30)
    at_risk_revenue = db.query(
        func.coalesce(func.sum(Quotation.net_total), 0) - func.coalesce(func.sum(Payment.amount), 0)
    ).outerjoin(Payment, Quotation.id == Payment.quotation_id).filter(
        Quotation.status == "converted",
        Quotation.updated_at < thirty_days_ago
    ).scalar()
    
    return {
        "revenue_forecast": {
            "historical_weekly": weekly_revenue,
            "predicted_next_week": predicted_next_week,
            "trend": "increasing" if weekly_revenue[-1] > weekly_revenue[-4] else "decreasing"
        },
        "patient_growth": {
            "historical_monthly": monthly_patients,
            "predicted_next_month": predicted_patients,
            "growth_rate": ((monthly_patients[-1] - monthly_patients[0]) / monthly_patients[0] * 100) if monthly_patients[0] > 0 else 0
        },
        "sample_metrics": {
            "avg_daily_samples": round(avg_daily_samples, 2),
            "predicted_tomorrow": round(avg_daily_samples),
        },
        "financial_health": {
            "collection_rate": round(collection_rate, 2),
            "at_risk_revenue": float(at_risk_revenue),
            "health_score": min(100, max(0, collection_rate))  # 0-100 score
        }
    }

from pydantic import BaseModel
from datetime import datetime
from typing import List


class QuotationTodayItem(BaseModel):
    quotation_id: int
    created_at: datetime


class PatientTodayQuotations(BaseModel):
    patient_id: int
    first_name: str
    last_name: str
    file_number: str | None
    quotations: List[QuotationTodayItem]

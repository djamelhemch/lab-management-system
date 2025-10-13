from sqlalchemy.orm import Session
from app.models.lab_result import LabResult
from app.models.quotation import Quotation, QuotationItem
from app.models.analysis import AnalysisCatalog, NormalRange, Unit
from app.models.patient import Patient
from app.models.lab_device import LabDevice as Device
from app.schemas.lab_result import LabResultCreate
from datetime import date
from typing import Optional
import logging
from datetime import datetime

logging.basicConfig(
    level=logging.DEBUG,
    format="%(asctime)s [%(levelname)s] %(message)s"
)
logger = logging.getLogger(__name__)

def compute_interpretation(result_value: float, normal_range: Optional[NormalRange]) -> str:
    """Determines if the result value is low, normal, or high based on normal range."""
    if not normal_range:
        return "n/a"
    try:
        value = float(result_value)
    except (ValueError, TypeError):
        return "n/a"

    if normal_range.normal_min is not None and value < normal_range.normal_min:
        return "low"
    if normal_range.normal_max is not None and value > normal_range.normal_max:
        return "high"
    return "normal"


def calculate_age(dob: date):
    """Returns (years, months) since date of birth."""
    if not dob:
        return None, None
    today = date.today()
    years = today.year - dob.year - ((today.month, today.day) < (dob.month, dob.day))
    months = (today.year - dob.year) * 12 + today.month - dob.month
    return years, months


def create_lab_result(db: Session, data):
    logging.info(f"🧪 Creating lab result for quotation_item_id={data.quotation_item_id}")

    quotation_item = db.query(QuotationItem).filter(QuotationItem.id == data.quotation_item_id).first()
    if not quotation_item:
        raise ValueError("Quotation item not found")
    logging.debug(f"Quotation item found → analysis_id={quotation_item.analysis_id}, quotation_id={quotation_item.quotation_id}")

    quotation = db.query(Quotation).filter(Quotation.id == quotation_item.quotation_id).first()
    if not quotation:
        raise ValueError("Quotation not found")
    logging.debug(f"Quotation found → patient_id={quotation.patient_id}")

    patient = db.query(Patient).filter(Patient.id == quotation.patient_id).first()
    if not patient:
        raise ValueError("Patient not found")

    # Normalize gender
    g = (patient.gender or "").strip().upper()
    if g in ["H", "M"]:
        sex = "M"
    elif g == "F":
        sex = "F"
    else:
        sex = "All"

    # Calculate age
    age_years, age_months = calculate_age(patient.dob)
    logging.debug(f"Patient → name={patient.first_name} {patient.last_name}, gender={g}, normalized={sex}, age={age_years} years ({age_months} months)")

    analysis = db.query(AnalysisCatalog).filter(AnalysisCatalog.id == quotation_item.analysis_id).first()
    if not analysis:
        raise ValueError("Analysis not found")
    logging.debug(f"Analysis → id={analysis.id}, name={analysis.name}, device_id={analysis.device_id}, unit_id={analysis.unit_id}")

    # Resolve device name
    device_name = None
    if analysis.device_id:
        try:
            device = db.query(Device).filter(Device.id == int(analysis.device_id)).first()
            device_name = device.name if device else None
        except ValueError:
            device_name = analysis.device_id
    logging.debug(f"✅ Device resolved → {device_name}")

    # Try to find a normal range match
    normal_range = (
        db.query(NormalRange)
        .filter(
            NormalRange.analysis_id == quotation_item.analysis_id,
            ((NormalRange.sex_applicable == sex) | (NormalRange.sex_applicable == "All")),
            ((NormalRange.age_min == None) | (age_years is None) | (age_years >= NormalRange.age_min)),
            ((NormalRange.age_max == None) | (age_years is None) | (age_years <= NormalRange.age_max)),
        )
        .first()
    )

    # Fallback if none found — try "All" ranges only
    if not normal_range:
        normal_range = (
            db.query(NormalRange)
            .filter(
                NormalRange.analysis_id == quotation_item.analysis_id,
                NormalRange.sex_applicable == "All",
            )
            .first()
        )
        if normal_range:
            logging.debug("🩸 Used fallback 'All' normal range")
        else:
            logging.warning("⚠️ No normal range found for this patient and analysis")

    normal_min = normal_range.normal_min if normal_range else None
    normal_max = normal_range.normal_max if normal_range else None

    # Determine interpretation
    interpretation = "n/a"
    try:
        value = float(data.result_value)
        if normal_min is not None and normal_max is not None:
            if value < 0.5 * normal_min or value > 1.5 * normal_max:
                interpretation = "critical"
            elif value < normal_min:
                interpretation = "low"
            elif value > normal_max:
                interpretation = "high"
            else:
                interpretation = "normal"
        else:
            logging.warning("⚠️ Cannot interpret result → missing normal range bounds")
    except (TypeError, ValueError):
        logging.warning("⚠️ Invalid result value, cannot interpret")

    result = LabResult(
        quotation_id=quotation_item.quotation_id,
        quotation_item_id=data.quotation_item_id,
        normal_range_id=normal_range.id if normal_range else None,
        result_value=data.result_value,
        interpretation=interpretation,
        status="final",
        device_name=device_name,
        normal_min=normal_min,
        normal_max=normal_max,
        created_at=datetime.utcnow(),
    )

    db.add(result)
    db.commit()
    db.refresh(result)

    logging.info(f"✅ Lab result created successfully → id={result.id}, interpretation={result.interpretation}")
    return result


def get_lab_result(db: Session, result_id: int):
    result = (
        db.query(
            LabResult,
            Quotation.id.label("quotation_id"),
            Patient.first_name,
            Patient.last_name,
            Patient.file_number,
            AnalysisCatalog.code.label("analysis_code"),
            AnalysisCatalog.name.label("analysis_name"),
        )
        .join(Quotation, Quotation.id == LabResult.quotation_id)
        .join(QuotationItem, QuotationItem.id == LabResult.quotation_item_id)
        .join(AnalysisCatalog, AnalysisCatalog.id == QuotationItem.analysis_id)
        .join(Patient, Patient.id == Quotation.patient_id)
        .filter(LabResult.id == result_id)
        .first()
    )

    if not result:
        return None

    # 🧩 Build a full dictionary for Laravel
    return {
        "id": result.LabResult.id,
        "quotation_id": result.quotation_id,
        "quotation_item_id": result.LabResult.quotation_item_id,
        "result_value": result.LabResult.result_value,
        "interpretation": result.LabResult.interpretation,
        "status": result.LabResult.status,
        "device_name": result.LabResult.device_name,
        "normal_min": result.LabResult.normal_min,
        "normal_max": result.LabResult.normal_max,
        "created_at": result.LabResult.created_at,
        "patient_first_name": result.first_name,
        "patient_last_name": result.last_name,
        "file_number": result.file_number,
        "analysis_code": result.analysis_code,
        "analysis_name": result.analysis_name,
    }



def list_lab_results(db: Session, skip: int = 0, limit: int = 100):
    results = (
        db.query(
            LabResult,
            Patient.first_name,
            Patient.last_name,
            Patient.file_number,
            AnalysisCatalog.code.label("analysis_code"),
            AnalysisCatalog.name.label("analysis_name")
        )
        .join(Quotation, Quotation.id == LabResult.quotation_id)
        .join(Patient, Patient.id == Quotation.patient_id)
        .join(QuotationItem, QuotationItem.id == LabResult.quotation_item_id)
        .join(AnalysisCatalog, AnalysisCatalog.id == QuotationItem.analysis_id)
        .order_by(LabResult.created_at.desc())
        .offset(skip)
        .limit(limit)
        .all()
    )

    return [
        {
            "id": r.LabResult.id,
            "quotation_id": r.LabResult.quotation_id,
            "quotation_item_id": r.LabResult.quotation_item_id,
            "analysis_code": r.analysis_code,
            "analysis_name": r.analysis_name,
            "result_value": r.LabResult.result_value,
            "interpretation": r.LabResult.interpretation,
            "status": r.LabResult.status,
            "device_name": r.LabResult.device_name,
            "normal_min": r.LabResult.normal_min,
            "normal_max": r.LabResult.normal_max,
            "created_at": r.LabResult.created_at,
            "patient_first_name": r.first_name,
            "patient_last_name": r.last_name,
            "file_number": r.file_number,
        }
        for r in results
    ]

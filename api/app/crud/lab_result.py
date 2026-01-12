from sqlalchemy.orm import Session
from app.models.lab_result import LabResult
from app.models.quotation import Quotation, QuotationItem
from app.models.analysis import AnalysisCatalog, NormalRange, Unit
from app.models.patient import Patient
from app.models.lab_device import LabDevice as Device

from app.schemas.lab_result import LabResultCreate, BulkLabResultCreate
from datetime import date
from typing import Optional
import logging
from datetime import datetime

logging.basicConfig(
    level=logging.DEBUG,
    format="%(asctime)s [%(levelname)s] %(message)s"
)
logger = logging.getLogger(__name__)


def compute_interpretation(value: Optional[float], normal_min: Optional[float], normal_max: Optional[float]) -> str:
    if value is None or normal_min is None or normal_max is None:
        return "n/a"
    if value < 0.5 * normal_min or value > 1.5 * normal_max:
        return "critical"
    if value < normal_min:
        return "low"
    if value > normal_max:
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

def resolve_device_name(db: Session, analysis: AnalysisCatalog):
    """Return device name from analysis.device_id if available"""
    if analysis.device_id:
        try:
            # If it's a numeric ID pointing to Device table
            device_id = int(analysis.device_id)
            device = db.query(Device).filter(Device.id == device_id).first()
            return device.name if device else None
        except ValueError:
            # If it's a text name
            return analysis.device_id
    return None

# === SINGLE LAB RESULT ===
def create_lab_result(db: Session, data: LabResultCreate):
    """Create a single lab result"""
    quotation_item = db.query(QuotationItem).filter(
        QuotationItem.id == data.quotation_item_id
    ).first()
    if not quotation_item:
        raise ValueError("Quotation item not found")

    analysis = quotation_item.analysis
    if not analysis:
        raise ValueError("Analysis not found")

    quotation = quotation_item.quotation
    if not quotation or not quotation.patient:
        raise ValueError("Quotation or patient not found")
    patient = quotation.patient

    # Normalize sex
    g = (patient.gender or "").strip().upper()
    sex = "M" if g in ["H", "M"] else ("F" if g == "F" else "All")
    age_years, _ = calculate_age(patient.dob)

    # Resolve normal range
    normal_range = (
        db.query(NormalRange)
        .filter(
            NormalRange.analysis_id == analysis.id,
            ((NormalRange.sex_applicable == sex) | (NormalRange.sex_applicable == "All")),
            ((NormalRange.age_min == None) | (age_years >= NormalRange.age_min)),
            ((NormalRange.age_max == None) | (age_years <= NormalRange.age_max)),
        )
        .first()
    )

    if not normal_range:
        normal_range = (
            db.query(NormalRange)
            .filter(
                NormalRange.analysis_id == analysis.id,
                NormalRange.sex_applicable == "All",
            )
            .first()
        )

    # Determine interpretation
    result_value = data.result_value
    normal_min = normal_range.normal_min if normal_range else None
    normal_max = normal_range.normal_max if normal_range else None
    interpretation = "n/a"
    try:
        value = float(result_value)
        if normal_min is not None and normal_max is not None:
            if value < 0.5 * normal_min or value > 1.5 * normal_max:
                interpretation = "critical"
            elif value < normal_min:
                interpretation = "low"
            elif value > normal_max:
                interpretation = "high"
            else:
                interpretation = "normal"
    except:
        interpretation = "n/a"

    # Resolve device
    device_name = resolve_device_name(db, analysis)

    # Save result
    lab_result = LabResult(
        quotation_id=quotation.id,
        quotation_item_id=quotation_item.id,
        normal_range_id=normal_range.id if normal_range else None,
        result_value=result_value,
        interpretation=interpretation,
        device_name=device_name,
        normal_min=normal_min,
        normal_max=normal_max,
        status="final",
        created_at=datetime.utcnow(),
    )
    db.add(lab_result)
    db.commit()
    db.refresh(lab_result)

    return lab_result

def create_lab_results_for_quotation(db: Session, quotation_id: int, result_values: dict):
    """Create lab results for all analyses inside a quotation"""
    quotation = db.query(Quotation).filter(Quotation.id == quotation_id).first()
    if not quotation or not quotation.patient:
        raise ValueError("Quotation or patient not found")
    patient = quotation.patient

    # Normalize sex
    g = (patient.gender or "").strip().upper()
    sex = "M" if g in ["H", "M"] else ("F" if g == "F" else "All")
    age_years, _ = calculate_age(patient.dob)

    created_results = []

    for item in quotation.analysis_items:
        analysis = item.analysis
        if not analysis:
            continue

        # Get result value
        result_value = result_values.get(item.id)
        try:
            value_float = float(result_value) if result_value is not None else None
        except ValueError:
            value_float = None

        # ✅ RESOLVE NORMAL RANGE FIRST
        normal_range = (
            db.query(NormalRange)
            .filter(
                NormalRange.analysis_id == analysis.id,
                ((NormalRange.sex_applicable == sex) | (NormalRange.sex_applicable == "All")),
                ((NormalRange.age_min == None) | (age_years >= NormalRange.age_min)),
                ((NormalRange.age_max == None) | (age_years <= NormalRange.age_max)),
            )
            .first()
        )
        if not normal_range:
            normal_range = (
                db.query(NormalRange)
                .filter(
                    NormalRange.analysis_id == analysis.id,
                    NormalRange.sex_applicable == "All",
                )
                .first()
            )

        # ✅ COMPUTE INTERPRETATION (normal_range now exists)
        normal_min = normal_range.normal_min if normal_range else None
        normal_max = normal_range.normal_max if normal_range else None
        interpretation = "n/a"
        try:
            if value_float is not None and normal_min is not None and normal_max is not None:
                if value_float < 0.5 * normal_min or value_float > 1.5 * normal_max:
                    interpretation = "critical"
                elif value_float < normal_min:
                    interpretation = "low"
                elif value_float > normal_max:
                    interpretation = "high"
                else:
                    interpretation = "normal"
        except:
            interpretation = "n/a"

        # Resolve device
        device_name = resolve_device_name(db, analysis)

        # Save result
        lab_result = LabResult(
            quotation_id=quotation.id,
            quotation_item_id=item.id,
            normal_range_id=normal_range.id if normal_range else None,
            result_value=result_value,
            interpretation=interpretation,
            device_name=device_name,
            normal_min=normal_min,
            normal_max=normal_max,
            status="final",
            created_at=datetime.utcnow(),
        )
        db.add(lab_result)
        created_results.append(lab_result)

    db.commit()
    for r in created_results:
        db.refresh(r)

    return created_results


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

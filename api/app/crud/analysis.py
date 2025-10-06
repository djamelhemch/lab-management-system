# crud/analysis.py
from sqlalchemy.orm import Session, joinedload
from sqlalchemy import or_
from typing import List, Optional
from app.models.lab_device import LabDevice

from app.models.analysis import (
    AnalysisCatalog,
    CategoryAnalyse,
    NormalRange,
)
from app.schemas.analysis import (
    AnalysisCreate,
    AnalysisUpdate,
    NormalRangeCreate,
    NormalRangeUpdate
)
import logging

class AnalysisCRUD:

    def create(self, db: Session, analysis_data: AnalysisCreate) -> AnalysisCatalog:
        ranges_data = getattr(analysis_data, "normal_ranges", [])
        device_ids = getattr(analysis_data, "device_ids", [])

        # ✅ Validate device IDs
        if device_ids:
            valid_devices = db.query(LabDevice.id).filter(LabDevice.id.in_(device_ids)).all()
            valid_ids = [d.id for d in valid_devices]
            if len(valid_ids) != len(device_ids):
                raise HTTPException(status_code=400, detail="One or more device IDs are invalid")
            device_id_str = ",".join(map(str, valid_ids))
        else:
            device_id_str = None

        db_analysis = AnalysisCatalog(
            **analysis_data.dict(exclude={"normal_ranges", "device_ids"}),
            device_id=device_id_str  # ✅ store as text
        )

        db.add(db_analysis)
        db.commit()
        db.refresh(db_analysis)

        # Handle normal ranges
        for r in ranges_data:
            db_range = NormalRange(analysis_id=db_analysis.id, **r.dict())
            db.add(db_range)

        db.commit()
        db.refresh(db_analysis)
        return db_analysis


    def get(self, db: Session, analysis_id: int):
        analysis = (
            db.query(AnalysisCatalog)
            .options(joinedload(AnalysisCatalog.normal_ranges))
            .filter(AnalysisCatalog.id == analysis_id)
            .first()
        )

        if not analysis:
            return None

        # ✅ Manually fetch related device names from TEXT field
        device_names = []
        if analysis.device_id:
            try:
                device_ids = [int(x.strip()) for x in analysis.device_id.split(',') if x.strip().isdigit()]
                if device_ids:
                    devices = db.query(LabDevice).filter(LabDevice.id.in_(device_ids)).all()
                    device_names = [d.name for d in devices]
            except Exception as e:
                print(f"[WARN] Failed to resolve device names: {e}")

        # Attach a transient attribute (not stored in DB)
        analysis.device_names = device_names
        return analysis

    def get_by_code(self, db: Session, code: str) -> Optional[AnalysisCatalog]:
        return (
            db.query(AnalysisCatalog)
            .options(joinedload(AnalysisCatalog.normal_ranges))
            .filter(AnalysisCatalog.code == code)
            .first()
        )

    def get_all(
        self,
        db: Session,
        skip: int = 0,
        limit: int = 100,
        category_analyse_id: Optional[int] = None,
        search: Optional[str] = None,
    ):
        query = db.query(AnalysisCatalog).options(joinedload(AnalysisCatalog.normal_ranges))

        if category_analyse_id:
            query = query.filter(AnalysisCatalog.category_analyse_id == category_analyse_id)

        if search:
            search_filter = f"%{search}%"
            query = query.filter(
                or_(
                    AnalysisCatalog.name.ilike(search_filter),
                    AnalysisCatalog.code.ilike(search_filter),
                    AnalysisCatalog.category_analyse.has(CategoryAnalyse.name.ilike(search_filter)),
                )
            )

        return query.offset(skip).limit(limit).all()

    def update(self, db: Session, analysis_id: int, analysis_data: AnalysisUpdate) -> Optional[AnalysisCatalog]:
        db_analysis = self.get(db, analysis_id)
        if not db_analysis:
            return None

        update_data = analysis_data.dict(exclude_unset=True, exclude={"normal_ranges", "device_ids"})

        # ✅ Handle device_ids list
        if analysis_data.device_ids is not None:
            device_ids = analysis_data.device_ids
            if device_ids:
                valid_devices = db.query(LabDevice.id).filter(LabDevice.id.in_(device_ids)).all()
                valid_ids = [d.id for d in valid_devices]
                if len(valid_ids) != len(device_ids):
                    raise HTTPException(status_code=400, detail="One or more device IDs are invalid")
                device_id_str = ",".join(map(str, valid_ids))
            else:
                device_id_str = None
            update_data["device_id"] = device_id_str

        # ✅ Apply updates
        for field, value in update_data.items():
            setattr(db_analysis, field, value)

        # ✅ Handle normal ranges if provided
        if analysis_data.normal_ranges is not None:
            db.query(NormalRange).filter(NormalRange.analysis_id == analysis_id).delete()
            for r in analysis_data.normal_ranges:
                db_range = NormalRange(analysis_id=analysis_id, **r.dict())
                db.add(db_range)

        db.commit()
        db.refresh(db_analysis)
        return db_analysis

    def delete(self, db: Session, analysis_id: int) -> bool:
        db_analysis = self.get(db, analysis_id)
        if db_analysis:
            db.delete(db_analysis)
            db.commit()
            return True
        return False


analysis_crud = AnalysisCRUD()

# crud/analysis.py
from sqlalchemy.orm import Session, joinedload
from sqlalchemy import or_
from typing import List, Optional

from app.models.analysis import (
    AnalysisCatalog,
    CategoryAnalyse,
    NormalRange
)
from app.schemas.analysis import (
    AnalysisCreate,
    AnalysisUpdate,
    NormalRangeCreate,
    NormalRangeUpdate
)


class AnalysisCRUD:
    def create(self, db: Session, analysis_data: AnalysisCreate) -> AnalysisCatalog:
        ranges_data = analysis_data.normal_ranges if hasattr(analysis_data, "normal_ranges") else []

        db_analysis = AnalysisCatalog(
            **analysis_data.dict(exclude={"normal_ranges"})
        )
        db.add(db_analysis)
        db.commit()
        db.refresh(db_analysis)

        # Create normal ranges
        for r in ranges_data:
            db_range = NormalRange(analysis_id=db_analysis.id, **r.dict())
            db.add(db_range)
        db.commit()
        db.refresh(db_analysis)

        return db_analysis

    def get(self, db: Session, analysis_id: int) -> Optional[AnalysisCatalog]:
        return (
            db.query(AnalysisCatalog)
            .options(joinedload(AnalysisCatalog.normal_ranges))
            .filter(AnalysisCatalog.id == analysis_id)
            .first()
        )

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

        update_data = analysis_data.dict(exclude_unset=True, exclude={"normal_ranges"})
        for field, value in update_data.items():
            setattr(db_analysis, field, value)

        # Handle ranges if provided
        if analysis_data.normal_ranges is not None:
            # Clear old ranges
            db.query(NormalRange).filter(NormalRange.analysis_id == analysis_id).delete()

            # Add new ranges
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

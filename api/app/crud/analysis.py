# crud/analysis.py
from sqlalchemy.orm import Session, joinedload
from sqlalchemy import or_
from app.models.analysis import AnalysisCatalog  # Import the updated model
from app.schemas.analysis import AnalysisCreate, AnalysisUpdate
from typing import List, Optional

class AnalysisCRUD:
    def create(self, db: Session, analysis_data: AnalysisCreate) -> AnalysisCatalog:
        db_analysis = AnalysisCatalog(**analysis_data.dict())
        db.add(db_analysis)
        db.commit()
        db.refresh(db_analysis)
        return db_analysis

    def get(self, db: Session, analysis_id: int) -> Optional[AnalysisCatalog]:
        return db.query(AnalysisCatalog).filter(AnalysisCatalog.id == analysis_id).first()

    def get_by_code(self, db: Session, code: str) -> Optional[AnalysisCatalog]:
        return db.query(AnalysisCatalog).filter(AnalysisCatalog.code == code).first()

    def get_all(self, db: Session, skip: int = 0, limit: int = 100,
                category_analyse_id: Optional[int] = None, search: Optional[str] = None) -> List[AnalysisCatalog]:
        query = db.query(AnalysisCatalog).options(
            joinedload(AnalysisCatalog.category_analyse),
            joinedload(AnalysisCatalog.unit),
            joinedload(AnalysisCatalog.sample_type)
        )

        if category_analyse_id:
            query = query.filter(AnalysisCatalog.category_analyse_id == category_analyse_id)

        if search:
            query = query.filter(
                or_(
                    AnalysisCatalog.name.ilike(f"%{search}%"),
                    AnalysisCatalog.code.ilike(f"%{search}%")
                )
            )

        return query.offset(skip).limit(limit).all()

    def update(self, db: Session, analysis_id: int, analysis_data: AnalysisUpdate) -> Optional[AnalysisCatalog]:
        db_analysis = self.get(db, analysis_id)
        if db_analysis:
            update_data = analysis_data.dict(exclude_unset=True)
            for field, value in update_data.items():
                setattr(db_analysis, field, value)
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

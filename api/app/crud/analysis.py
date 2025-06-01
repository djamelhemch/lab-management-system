# crud/analysis.py
from sqlalchemy.orm import Session
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
                category: Optional[str] = None, search: Optional[str] = None) -> List[AnalysisCatalog]:
        query = db.query(AnalysisCatalog)  # Removed is_active filter

        if category:
            query = query.filter(AnalysisCatalog.category == category)

        if search:
            query = query.filter(
                or_(
                    AnalysisCatalog.name.ilike(f"%{search}%"),
                    AnalysisCatalog.code.ilike(f"%{search}%"),
                    AnalysisCatalog.category.ilike(f"%{search}%")
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
            db.delete(db_analysis)  # Hard delete instead of soft delete
            db.commit()
            return True
        return False

    def get_categories(self, db: Session) -> List[str]:
        return [cat[0] for cat in db.query(AnalysisCatalog.category).distinct().all() if cat[0]]

analysis_crud = AnalysisCRUD()
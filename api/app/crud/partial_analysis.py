from sqlalchemy.orm import Session  
from typing import List, Optional
from app.models.analysis import CategoryAnalyse, SampleType, Unit
from app.schemas.analysis import CategoryAnalyseCreate, SampleTypeCreate, UnitCreate

class CategoryAnalyseCRUD:  
    def create(self, db: Session, category_analyse_data: CategoryAnalyseCreate) -> CategoryAnalyse:  
        db_category_analyse = CategoryAnalyse(name=category_analyse_data.name)  
        db.add(db_category_analyse)  
        db.commit()  
        db.refresh(db_category_analyse)  
        return db_category_analyse  
  
    def get_by_name(self, db: Session, name: str) -> Optional[CategoryAnalyse]:  
        return db.query(CategoryAnalyse).filter(CategoryAnalyse.name == name).first()  
  
    def get_all(self, db: Session) -> List[CategoryAnalyse]:  
        return db.query(CategoryAnalyse).order_by(CategoryAnalyse.name).all()  
  
    def get_or_create(self, db: Session, name: str) -> CategoryAnalyse:  
        category_analyse = self.get_by_name(db, name)  
        if not category_analyse:  
            category_analyse = self.create(db, CategoryAnalyseCreate(name=name))  
        return category_analyse  
  
class SampleTypeCRUD:  
    def create(self, db: Session, sample_type_data: SampleTypeCreate) -> SampleType:  
        db_sample_type = SampleType(name=sample_type_data.name)  
        db.add(db_sample_type)  
        db.commit()  
        db.refresh(db_sample_type)  
        return db_sample_type  
  
    def get_by_name(self, db: Session, name: str) -> Optional[SampleType]:  
        return db.query(SampleType).filter(SampleType.name == name).first()  
  
    def get_all(self, db: Session) -> List[SampleType]:  
        return db.query(SampleType).order_by(SampleType.name).all()  
  
    def get_or_create(self, db: Session, name: str) -> SampleType:  
        sample_type = self.get_by_name(db, name)  
        if not sample_type:  
            sample_type = self.create(db, SampleTypeCreate(name=name))  
        return sample_type  
  
class UnitCRUD:  
    def create(self, db: Session, unit_data: UnitCreate) -> Unit:  
        db_unit = Unit(name=unit_data.name)  
        db.add(db_unit)  
        db.commit()  
        db.refresh(db_unit)  
        return db_unit  
  
    def get_by_name(self, db: Session, name: str) -> Optional[Unit]:  
        return db.query(Unit).filter(Unit.name == name).first()  
  
    def get_all(self, db: Session) -> List[Unit]:  
        return db.query(Unit).order_by(Unit.name).all()  
  
    def get_or_create(self, db: Session, name: str) -> Unit:  
        unit = self.get_by_name(db, name)  
        if not unit:  
            unit = self.create(db, UnitCreate(name=name))  
        return unit  
  
# Initialize CRUD instances  
category_analyse_crud = CategoryAnalyseCRUD()  
sample_type_crud = SampleTypeCRUD()  
unit_crud = UnitCRUD()
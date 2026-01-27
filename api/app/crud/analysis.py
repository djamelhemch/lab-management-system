# crud/analysis.py
from sqlalchemy.orm import Session, joinedload
from sqlalchemy import or_, text  
from typing import List, Optional
from app.models.lab_device import LabDevice
from fastapi import HTTPException  
from app.models.analysis import (
    AnalysisCatalog,
    CategoryAnalyse,
    NormalRange,
    SampleType,
    analysis_sample_types
)
from app.schemas.analysis import (
    AnalysisCreate,
    AnalysisUpdate,
    NormalRangeCreate,
    NormalRangeUpdate,
    
)
import logging
logger = logging.getLogger(__name__)

class AnalysisCRUD:


    def create(self, db: Session, analysis_data: AnalysisCreate) -> AnalysisCatalog:
        ranges_data = getattr(analysis_data, "normal_ranges", [])
        device_ids = getattr(analysis_data, "device_ids", [])
        sample_type_ids = getattr(analysis_data, "sample_type_ids", [])

        # Validate device IDs
        device_id_str = None
        if device_ids:
            valid_devices = db.query(LabDevice.id).filter(LabDevice.id.in_(device_ids)).all()
            valid_ids = [d.id for d in valid_devices]
            if len(valid_ids) != len(device_ids):
                raise HTTPException(status_code=400, detail="One or more device IDs are invalid")
            device_id_str = ",".join(map(str, valid_ids))

        # Validate sample types (remove duplicates)
        sample_types = []
        if sample_type_ids:
            sample_type_ids = list(dict.fromkeys(sample_type_ids))
            sample_types = db.query(SampleType).filter(SampleType.id.in_(sample_type_ids)).all()
            if len(sample_types) != len(sample_type_ids):
                raise HTTPException(status_code=400, detail="One or more sample type IDs are invalid")

        # Prepare data
        analysis_dict = analysis_data.dict(exclude={"normal_ranges", "device_ids", "sample_type_ids"})
        analysis_dict['device_id'] = device_id_str
        analysis_dict['is_active'] = analysis_dict.get('is_active', True)
        analysis_dict.pop('sample_type_id', None)

        try:
            # Create analysis
            db_analysis = AnalysisCatalog(**analysis_dict)
            db.add(db_analysis)
            db.flush()  # Get ID
            
            logger.info(f"Created analysis with ID: {db_analysis.id}")
            
            # ✅ Add sample types using SQLAlchemy relationship (like update method)
            if sample_types:
                db_analysis.sample_types = sample_types
                logger.info(f"Associated {len(sample_types)} sample types")

            # Add normal ranges
            for r in ranges_data:
                db_range = NormalRange(analysis_id=db_analysis.id, **r.dict())
                db.add(db_range)

            # Commit everything
            db.commit()
            db.refresh(db_analysis)
            
            logger.info(f"✅ Successfully created analysis ID: {db_analysis.id} with {len(db_analysis.sample_types)} sample types")
            return db_analysis
            
        except Exception as e:
            db.rollback()
            logger.error(f"Error creating analysis: {str(e)}", exc_info=True)
            raise HTTPException(status_code=500, detail=f"Failed to create analysis: {str(e)}")


    def update(self, db: Session, analysis_id: int, analysis_data: AnalysisUpdate) -> AnalysisCatalog:
        db_analysis = db.query(AnalysisCatalog).filter(AnalysisCatalog.id == analysis_id).first()
        if not db_analysis:
            raise HTTPException(status_code=404, detail="Analysis not found")

        ranges_data = getattr(analysis_data, "normal_ranges", None)
        device_ids = getattr(analysis_data, "device_ids", None)
        sample_type_ids = getattr(analysis_data, "sample_type_ids", None)

        try:
            # Handle device IDs
            if device_ids is not None:
                if device_ids:
                    valid_devices = db.query(LabDevice.id).filter(LabDevice.id.in_(device_ids)).all()
                    valid_ids = [d.id for d in valid_devices]
                    if len(valid_ids) != len(device_ids):
                        raise HTTPException(status_code=400, detail="One or more device IDs are invalid")
                    device_id_str = ",".join(map(str, valid_ids))
                else:
                    device_id_str = None
                db_analysis.device_id = device_id_str

            # ✅ Handle sample types (many-to-many) - Clear and reassign
            if sample_type_ids is not None:
                # Remove duplicates
                sample_type_ids = list(set(sample_type_ids))
                
                if sample_type_ids:
                    sample_types = db.query(SampleType).filter(SampleType.id.in_(sample_type_ids)).all()
                    if len(sample_types) != len(sample_type_ids):
                        raise HTTPException(status_code=400, detail="One or more sample type IDs are invalid")
                    
                    # ✅ Clear existing associations first, then set new ones
                    db_analysis.sample_types = []
                    db.flush()  # Flush to clear associations
                    db_analysis.sample_types = sample_types
                else:
                    # Clear all sample types
                    db_analysis.sample_types = []

            # Update normal ranges
            if ranges_data is not None:
                db.query(NormalRange).filter(NormalRange.analysis_id == analysis_id).delete()
                db.flush()
                for r in ranges_data:
                    db_range = NormalRange(analysis_id=analysis_id, **r.dict())
                    db.add(db_range)

            # Update other fields
            update_data = analysis_data.dict(exclude_unset=True, exclude={"normal_ranges", "device_ids", "sample_type_ids"})
            update_data.pop('sample_type_id', None)
            
            for key, value in update_data.items():
                setattr(db_analysis, key, value)

            db.commit()
            db.refresh(db_analysis)
            
            logger.info(f"✅ Updated analysis ID: {analysis_id} with {len(db_analysis.sample_types)} sample types")
            return db_analysis
            
        except HTTPException:
            db.rollback()
            raise
        except Exception as e:
            db.rollback()
            logger.error(f"Error updating analysis {analysis_id}: {str(e)}")
            raise HTTPException(status_code=500, detail=f"Failed to update analysis: {str(e)}")

    def get(self, db: Session, analysis_id: int) -> dict:
        analysis = db.query(AnalysisCatalog).filter(AnalysisCatalog.id == analysis_id).first()
        if not analysis:
            return None
        
        # ✅ Serialize sample types (many-to-many)
        sample_type_ids = [st.id for st in analysis.sample_types]
        sample_types = [
            {
                "id": st.id,
                "name": st.name,
                "description": getattr(st, 'description', None),
                "created_at": st.created_at
            } 
            for st in analysis.sample_types
        ]
        
        # Device serialization
        device_ids = []
        device_names = []
        if analysis.device_id:
            device_id_list = [int(d) for d in analysis.device_id.split(',') if d]
            devices = db.query(LabDevice).filter(LabDevice.id.in_(device_id_list)).all()
            device_ids = [d.id for d in devices]
            device_names = [d.name for d in devices]
        
        return {
            **analysis.__dict__,
            "sample_type_ids": sample_type_ids,
            "sample_types": sample_types,
            "device_ids": device_ids,
            "device_names": device_names,
            "category_analyse": analysis.category_analyse.__dict__ if analysis.category_analyse else None,
            "unit": analysis.unit.__dict__ if analysis.unit else None,
            "normal_ranges": [nr.__dict__ for nr in analysis.normal_ranges]
        }

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
        is_active: Optional[bool] = None
    ) -> List[dict]:
        query = db.query(AnalysisCatalog)
        
        # Apply filters
        if category_analyse_id:
            query = query.filter(AnalysisCatalog.category_analyse_id == category_analyse_id)
        if is_active is not None:
            query = query.filter(AnalysisCatalog.is_active == is_active)
        if search:
            query = query.filter(
                (AnalysisCatalog.name.ilike(f"%{search}%")) | 
                (AnalysisCatalog.code.ilike(f"%{search}%"))
            )
        
        analyses = query.offset(skip).limit(limit).all()
        
        # ✅ Serialize each analysis with sample types
        result = []
        for analysis in analyses:
            sample_type_ids = [st.id for st in analysis.sample_types]
            sample_types = [
                {
                    "id": st.id,
                    "name": st.name,
                    "description": getattr(st, 'description', None),
                    "created_at": st.created_at
                } 
                for st in analysis.sample_types
            ]
            
            # Device serialization
            device_ids = []
            device_names = []
            if analysis.device_id:
                device_id_list = [int(d) for d in analysis.device_id.split(',') if d]
                devices = db.query(LabDevice).filter(LabDevice.id.in_(device_id_list)).all()
                device_ids = [d.id for d in devices]
                device_names = [d.name for d in devices]
            
            result.append({
                **analysis.__dict__,
                "sample_type_ids": sample_type_ids,
                "sample_types": sample_types,
                "device_ids": device_ids,
                "device_names": device_names,
                "category_analyse": analysis.category_analyse.__dict__ if analysis.category_analyse else None,
                "unit": analysis.unit.__dict__ if analysis.unit else None,
                "normal_ranges": [nr.__dict__ for nr in analysis.normal_ranges]
            })
        
        return result
       
    def get_active(
        self,
        db: Session,
        skip: int = 0,
        limit: int = 100,
        category_analyse_id: Optional[int] = None,
        search: Optional[str] = None,
    ):
        query = (
            db.query(AnalysisCatalog)
            .options(joinedload(AnalysisCatalog.normal_ranges))
            .filter(AnalysisCatalog.is_active == True)
        )

        if category_analyse_id:
            query = query.filter(AnalysisCatalog.category_analyse_id == category_analyse_id)

        if search:
            search_filter = f"%{search}%"
            query = query.filter(
                or_(
                    AnalysisCatalog.name.ilike(search_filter),
                    AnalysisCatalog.code.ilike(search_filter),
                    AnalysisCatalog.category_analyse.has(
                        CategoryAnalyse.name.ilike(search_filter)
                    ),
                )
            )

        return query.offset(skip).limit(limit).all()

    def update(self, db: Session, analysis_id: int, analysis_data: AnalysisUpdate) -> AnalysisCatalog:
        db_analysis = db.query(AnalysisCatalog).filter(AnalysisCatalog.id == analysis_id).first()
        if not db_analysis:
            raise HTTPException(status_code=404, detail="Analysis not found")

        ranges_data = getattr(analysis_data, "normal_ranges", None)
        device_ids = getattr(analysis_data, "device_ids", None)
        sample_type_ids = getattr(analysis_data, "sample_type_ids", None)  # ✅ ADD

        # Handle device IDs
        if device_ids is not None:
            if device_ids:
                valid_devices = db.query(LabDevice.id).filter(LabDevice.id.in_(device_ids)).all()
                valid_ids = [d.id for d in valid_devices]
                if len(valid_ids) != len(device_ids):
                    raise HTTPException(status_code=400, detail="One or more device IDs are invalid")
                device_id_str = ",".join(map(str, valid_ids))
            else:
                device_id_str = None
            db_analysis.device_id = device_id_str

        # ✅ Handle sample types (many-to-many)
        if sample_type_ids is not None:
            if sample_type_ids:
                sample_types = db.query(SampleType).filter(SampleType.id.in_(sample_type_ids)).all()
                if len(sample_types) != len(sample_type_ids):
                    raise HTTPException(status_code=400, detail="One or more sample type IDs are invalid")
                db_analysis.sample_types = sample_types
            else:
                db_analysis.sample_types = []

        # Update normal ranges
        if ranges_data is not None:
            db.query(NormalRange).filter(NormalRange.analysis_id == analysis_id).delete()
            for r in ranges_data:
                db_range = NormalRange(analysis_id=analysis_id, **r.dict())
                db.add(db_range)

        # Update other fields
        update_data = analysis_data.dict(exclude_unset=True, exclude={"normal_ranges", "device_ids", "sample_type_ids"})
        
        # ✅ Remove old sample_type_id if present
        update_data.pop('sample_type_id', None)
        
        for key, value in update_data.items():
            setattr(db_analysis, key, value)

        db.commit()
        db.refresh(db_analysis)
        return db_analysis

    def delete(self, db: Session, analysis_id: int) -> bool:
        db_analysis = self.get(db, analysis_id)
        if not db_analysis:
            return False

        db_analysis.is_active = False
        db.commit()
        return True


analysis_crud = AnalysisCRUD()

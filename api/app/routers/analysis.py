# routers/analysis.py
from fastapi import APIRouter, Depends, HTTPException, Query, Request
from sqlalchemy.orm import Session
from typing import List, Optional
from app.database import get_db
from app.schemas.analysis import AnalysisCreate, AnalysisUpdate, AnalysisResponse
from app.crud.analysis import analysis_crud
from app.schemas.analysis import CategoryAnalyseCreate, CategoryAnalyseResponse, SampleTypeCreate, SampleTypeResponse, UnitCreate, UnitResponse
from app.crud.partial_analysis import category_analyse_crud, sample_type_crud, unit_crud
from app.models.user import User
from app.routers.auth import get_current_user
from app.utils.app_logging import log_action, log_route  # Add this import, adjust path if needed

from fastapi import APIRouter, Depends, HTTPException, Query
from sqlalchemy.orm import Session

import logging
logger = logging.getLogger("uvicorn.error")


router = APIRouter(prefix="/analyses", tags=["analyses"])




# Lookup table endpoints
@router.post("/category-analyse", response_model=CategoryAnalyseResponse)  
def create_category_analyse(
    category_analyse: CategoryAnalyseCreate, 
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    request: Request = None
):  
    try:  
        db_category_analyse = category_analyse_crud.get_by_name(db, category_analyse.name)  
        if db_category_analyse:  
            raise HTTPException(status_code=400, detail="Category already exists")  
        new_category = category_analyse_crud.create(db, category_analyse)
        log_action(
            db=db,
            user_id=current_user.id,
            action_type="create_category_analyse",
            description=f"Category '{new_category.name}' created",
            request=request
        )
        return new_category
    except Exception as e:  
        logger.error(f"Error creating category: {e}")  
        raise HTTPException(status_code=500, detail="Internal server error")


@router.get("/category-analyse", response_model=List[CategoryAnalyseResponse])
def get_category_analyse(db: Session = Depends(get_db)):
    return category_analyse_crud.get_all(db)


@router.post("/sample-types", response_model=SampleTypeResponse)
def create_sample_type(
    sample_type: SampleTypeCreate, 
    db: Session = Depends(get_db), 
    current_user: User = Depends(get_current_user), 
    request: Request = None
):
    db_sample_type = sample_type_crud.get_by_name(db, sample_type.name)
    if db_sample_type:
        raise HTTPException(status_code=400, detail="Sample type already exists")
    new_sample_type = sample_type_crud.create(db, sample_type)

    log_action(
        db=db,
        user_id=current_user.id,
        action_type="create_sample_type",
        description=f"Sample type '{new_sample_type.name}' created",
        request=request
    )
    return new_sample_type


@router.get("/sample-types", response_model=List[SampleTypeResponse])
def get_sample_types(db: Session = Depends(get_db)):
    return sample_type_crud.get_all(db)


@router.post("/units", response_model=UnitResponse)
@log_route("create_unit")
def create_unit(unit: UnitCreate, db: Session = Depends(get_db)):
    db_unit = unit_crud.get_by_name(db, unit.name)
    if db_unit:
        raise HTTPException(status_code=400, detail="Unit already exists")
    return unit_crud.create(db, unit)


@router.get("/units", response_model=List[UnitResponse])
def get_units(db: Session = Depends(get_db)):
    return unit_crud.get_all(db)

# ========================================
# ANALYSIS ENDPOINTS
# ========================================

@router.get("/table", response_model=List[AnalysisResponse])  
def get_analyses_table(  
    skip: int = 0,  
    limit: int = 100,  
    category_analyse_id: Optional[int] = Query(None),  
    q: Optional[str] = Query(None),  
    is_active: Optional[bool] = Query(None),
    db: Session = Depends(get_db),  
):  
    logger.info(f"=== GET /analyses/table called ===")  
    logger.info(f"Raw params - skip: {skip}, limit: {limit}")  
    logger.info(f"Raw params - category_analyse_id: {category_analyse_id}")  
    logger.info(f"Raw params - q: '{q}', is_active: {is_active}")  
  
    try:  
        logger.info("Calling analysis_crud.get_all...")  
        results = analysis_crud.get_all(  
            db,  
            skip=skip,  
            limit=limit,  
            category_analyse_id=category_analyse_id,  
            search=q,  
            is_active=is_active,
        )  
        logger.info(f"analysis_crud.get_all returned {len(results)} results")  
        logger.info(f"=== GET /analyses/table completed successfully ===")  
        return results  
    except Exception as e:  
        logger.error(f"=== ERROR in get_analyses_table ===")  
        logger.error(f"Exception: {str(e)}", exc_info=True)  
        raise HTTPException(status_code=500, detail=f"Internal server error: {str(e)}")


@router.post("/", response_model=AnalysisResponse)
@log_route("create_analysis")
async def create_analysis(
    request: Request,
    analysis: AnalysisCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    # ✅ Log raw request body
    try:
        body = await request.body()
        body_str = body.decode('utf-8')
        logger.info(f"=== RAW REQUEST BODY ===")
        logger.info(body_str)
        logger.info(f"=== END RAW BODY ===")
    except Exception as e:
        logger.error(f"Could not read request body: {e}")
    
    # ✅ Log parsed Pydantic model with sample_type_ids
    logger.info(f"=== PARSED ANALYSIS MODEL ===")
    logger.info(f"name: {analysis.name}")
    logger.info(f"code: {analysis.code}")
    logger.info(f"tube_type: {analysis.tube_type}")
    logger.info(f"sample_type_id (deprecated): {analysis.sample_type_id}")
    logger.info(f"sample_type_ids (new): {analysis.sample_type_ids}")  # ✅ NEW
    logger.info(f"device_ids type: {type(analysis.device_ids)}, value: {analysis.device_ids}")
    logger.info(f"normal_ranges count: {len(analysis.normal_ranges) if analysis.normal_ranges else 0}")
    
    if analysis.normal_ranges:
        for idx, nr in enumerate(analysis.normal_ranges):
            logger.info(f"  Range {idx}: sex={nr.sex_applicable}, age_min={nr.age_min}, age_max={nr.age_max}")
    
    logger.info(f"=== END PARSED MODEL ===")

    # Check if code exists
    if analysis.code and analysis_crud.get_by_code(db, analysis.code):
        raise HTTPException(status_code=400, detail="Analysis code already exists")

    # ✅ Create analysis with multiple sample types
    result = analysis_crud.create(db, analysis)
    logger.info(f"✅ Created analysis ID: {result.id}")
    logger.info(f"✅ Associated sample types: {[st.name for st in result.sample_types]}")

    return result


@router.get("/", response_model=List[AnalysisResponse])
def get_analyses(
    skip: int = 0,
    limit: int = 100,
    category_analyse_id: Optional[int] = Query(None),
    search: Optional[str] = Query(None),
    db: Session = Depends(get_db),
    is_active: Optional[bool] = Query(None)
):
    return analysis_crud.get_all(
        db, 
        skip=skip, 
        limit=limit, 
        category_analyse_id=category_analyse_id, 
        search=search, 
        is_active=is_active
    )


@router.get("/{analysis_id}", response_model=AnalysisResponse)  
def get_analysis(analysis_id: int, db: Session = Depends(get_db)):  
    analysis = analysis_crud.get(db, analysis_id)  
    if not analysis:  
        raise HTTPException(status_code=404, detail="Analysis not found")
    
    # ✅ Log sample types for debugging
    logger.info(f"Analysis {analysis_id} has {len(analysis.get('sample_types', []))} sample types")
    
    return analysis


@router.put("/{analysis_id}", response_model=AnalysisResponse)
@log_route("update_analysis")
async def update_analysis(
    analysis_id: int, 
    analysis: AnalysisUpdate, 
    db: Session = Depends(get_db), 
    current_user: User = Depends(get_current_user), 
    request: Request = None
):
    logger.info(f"=== UPDATING ANALYSIS {analysis_id} ===")
    logger.info(f"is_active received: {analysis.is_active}")
    logger.info(f"sample_type_id (deprecated): {analysis.sample_type_id}")
    logger.info(f"sample_type_ids (new): {analysis.sample_type_ids}")  # ✅ NEW
    logger.info(f"Full data: {analysis.dict(exclude_unset=True)}")
    
    db_analysis = analysis_crud.update(db, analysis_id, analysis)
    if not db_analysis:
        raise HTTPException(status_code=404, detail="Analysis not found")
    
    logger.info(f"✅ Updated analysis {analysis_id}")
    logger.info(f"✅ Sample types after update: {[st.name for st in db_analysis.sample_types]}")
    
    return db_analysis


@router.delete("/{analysis_id}", response_model=dict)
@log_route("delete_analysis")
def delete_analysis(analysis_id: int, db: Session = Depends(get_db)):
    success = analysis_crud.delete(db, analysis_id)
    if not success:
        raise HTTPException(status_code=404, detail="Analysis not found")
    return {"message": "Analysis deleted successfully"}
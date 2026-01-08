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
def create_category_analyse(category_analyse: CategoryAnalyseCreate, db: Session = Depends(get_db),  current_user: User = Depends(get_current_user),
    request: Request = None):  
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
def create_sample_type(sample_type: SampleTypeCreate, db: Session = Depends(get_db), current_user: User = Depends(get_current_user), request: Request = None):
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
    logger.info(f"Raw params - skip: {skip} (type: {type(skip)})")  
    logger.info(f"Raw params - limit: {limit} (type: {type(limit)})")  
    logger.info(  
        f"Raw params - category_analyse_id: {category_analyse_id} (type: {type(category_analyse_id)})"  
    )  
    logger.info(f"Raw params - q: '{q}' (type: {type(q)})")  
  
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
        logger.error(f"Exception type: {type(e)}")  
        logger.error(f"Exception message: {str(e)}")  
        logger.error(f"Exception details:", exc_info=True)  
        raise HTTPException(status_code=500, detail=f"Internal server error: {str(e)}")
    
#analysis endpoints
@router.post("/", response_model=AnalysisResponse)
@log_route("create_analysis")
async def create_analysis(
    analysis: AnalysisCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
    request: Request = None
):
    # Log the raw request JSON
    try:
        body = await request.json()  # only works if you make this function async
    except Exception:
        body = "Could not parse JSON"
    logging.info(f"Received analysis payload: {body}")

    # Check required fields explicitly
    logging.info(f"tube_type: {analysis.tube_type}")
    logging.info(f"device_ids: {analysis.device_ids}")

    # Check if code exists
    if analysis.code and analysis_crud.get_by_code(db, analysis.code):
        raise HTTPException(status_code=400, detail="Analysis code already exists")

    result = analysis_crud.create(db, analysis)

    logging.info(f"Created analysis ID: {result.id}")

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
    return analysis_crud.get_all(db, skip=skip, limit=limit, category_analyse_id=category_analyse_id, search=search, is_active=is_active)


@router.get("/{analysis_id}", response_model=AnalysisResponse)  
def get_analysis(analysis_id: int, db: Session = Depends(get_db)):  
    analysis = analysis_crud.get(db, analysis_id)  
    if not analysis:  
        raise HTTPException(status_code=404, detail="Analysis not found")  
    return analysis

@router.put("/{analysis_id}", response_model=AnalysisResponse)
@log_route("update_analysis")
def update_analysis(analysis_id: int, analysis: AnalysisUpdate, db: Session = Depends(get_db), current_user: User = Depends(get_current_user), request: Request = None):
    db_analysis = analysis_crud.update(db, analysis_id, analysis)
    if not db_analysis:
        raise HTTPException(status_code=404, detail="Analysis not found")
    return db_analysis


@router.delete("/{analysis_id}", response_model=dict)
@log_route("delete_analysis")
def delete_analysis(analysis_id: int, db: Session = Depends(get_db)):
    success = analysis_crud.delete(db, analysis_id)
    if not success:
        raise HTTPException(status_code=404, detail="Analysis not found")
    return {"message": "Analysis deleted successfully"}
from fastapi import FastAPI
from fastapi.staticfiles import StaticFiles
from app.routers import auth, agreement, patient, doctor, dashboard, sample,analysis, quotation,queue,leave_request, profile, payment, logs, settings,lab_device,lab_formulas,lab_results,analytics
from fastapi.middleware.cors import CORSMiddleware
import time

start = time.time()
print(">>> Starting FastAPI setup...")

app = FastAPI(title="Laboratory Information System")
# Mount static files
app.mount("/static", StaticFiles(directory="uploads/profile_photos"), name="static")

origins = [  
    "http://localhost",  
    "http://localhost:8080",  # Or the origin of your Laravel app  
    "http://127.0.0.1",  
    "http://127.0.0.1:8080",  
    "*", # only for development  
    "https://abdelatiflab.hemchracing.com",
    "https://app.hemchracing.com"
]  

app.add_middleware(  
    CORSMiddleware,  
    allow_origins=origins,  
    allow_credentials=True,  
    allow_methods=["*"],  # Allows all HTTP methods  
    allow_headers=["*"],  # Allows all headers  
)

app.include_router(patient.router)
app.include_router(doctor.router)
app.include_router(dashboard.router)
app.include_router(sample.router)
app.include_router(analysis.router)
app.include_router(quotation.router)
app.include_router(queue.router)
app.include_router(agreement.router)
app.include_router(auth.router)
app.include_router(leave_request.router)
app.include_router(profile.router)
app.include_router(payment.router)
app.include_router(logs.router)
app.include_router(settings.router)
app.include_router(lab_device.router)
app.include_router(lab_formulas.router)
app.include_router(lab_results.router)
app.include_router(analytics.router)
@app.get("/")
def root():
    return {"message": "LIS API is running"}

print(">>> App and routers set up in", time.time() - start, "seconds")
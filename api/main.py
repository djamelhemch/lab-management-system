from fastapi import FastAPI
from app.routers import auth, agreement, patient, doctor, dashboard, sample,analysis, quotation,queue
from fastapi.middleware.cors import CORSMiddleware
import time

start = time.time()
print(">>> Starting FastAPI setup...")

app = FastAPI(title="Laboratory Information System")

origins = [  
    "http://localhost",  
    "http://localhost:8080",  # Or the origin of your Laravel app  
    "http://127.0.0.1",  
    "http://127.0.0.1:8080",  
    "*", # only for development  
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

@app.get("/")
def root():
    return {"message": "LIS API is running"}

print(">>> App and routers set up in", time.time() - start, "seconds")
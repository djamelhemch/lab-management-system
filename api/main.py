from fastapi import FastAPI
from app.routers import auth, agreement, patient, doctor, dashboard, sample,analysis, quotation,queue
from fastapi.middleware.cors import CORSMiddleware
import time

start = time.time()
print(">>> Starting FastAPI setup...")

app = FastAPI(title="Laboratory Information System")

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
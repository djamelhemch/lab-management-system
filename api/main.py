from fastapi import FastAPI
from app.routers import patient, doctor, dashboard, sample,analysis

app = FastAPI(title="Laboratory Information System")

app.include_router(patient.router)
app.include_router(doctor.router)
app.include_router(dashboard.router)
app.include_router(sample.router)
app.include_router(analysis.router)

@app.get("/")
def root():
    return {"message": "LIS API is running"}

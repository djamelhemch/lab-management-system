<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\SamplesController;
use App\Http\Controllers\QuotationController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');



Route::get('/patients/table', [PatientController::class, 'table'])->name('patients.table');
Route::resource('patients', PatientController::class);

Route::get('/doctors/table', [DoctorController::class, 'table'])->name('doctors.table');
Route::get('/doctors/{doctor}/patients/table', [DoctorController::class, 'patientsTable'])->name('doctors.patients.table');
Route::resource('doctors', DoctorController::class)->only(['index', 'create', 'store','show', 'edit', 'update']);


Route::resource('reports', ReportController::class);


Route::get('/analyses/table', [AnalysisController::class, 'table'])->name('analyses.table');  
Route::post('/analyses/category-analyse', [AnalysisController::class, 'storeCategory']);  
Route::post('/analyses/sample-types', [AnalysisController::class, 'storeSampleType']);  
Route::post('/analyses/units', [AnalysisController::class, 'storeUnit']);  
Route::resource('analyses', AnalysisController::class);


Route::resource('samples', SamplesController::class);
Route::put('samples/{id}/status', [SamplesController::class, 'updateStatus'])->name('samples.updateStatus');
  
Route::get('api/search/patients', [SamplesController::class, 'searchPatients'])->name('api.search.patients');  
Route::get('api/search/doctors', [SamplesController::class, 'searchDoctors'])->name('api.search.doctors');

Route::resource('quotations', QuotationController::class);
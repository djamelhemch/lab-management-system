<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\SamplesController;
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');


Route::resource('patients', PatientController::class);
Route::resource('doctors', DoctorController::class)->only(['index', 'create', 'store']);


Route::resource('reports', ReportController::class);
Route::resource('analyses', AnalysisController::class);

Route::resource('samples', SamplesController::class);
Route::put('samples/{id}/status', [SamplesController::class, 'updateStatus'])->name('samples.updateStatus');
  
Route::get('api/search/patients', [SamplesController::class, 'searchPatients'])->name('api.search.patients');  
Route::get('api/search/doctors', [SamplesController::class, 'searchDoctors'])->name('api.search.doctors');
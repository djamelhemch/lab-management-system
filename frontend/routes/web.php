<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;

use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');


Route::resource('patients', PatientController::class);
Route::resource('doctors', DoctorController::class)->only(['index', 'create', 'store']);

// web.php
Route::resource('samples', SampleController::class);
Route::resource('reports', ReportController::class);

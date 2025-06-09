<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    PatientController,
    DoctorController,
    DashboardController,
    AnalysisController,
    SamplesController,
    QuotationController,
    AuthController,
    Admin\UserController,
    AgreementController,
    QueueController
};

// Authentication routes
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes for authenticated users (admin + user)
Route::middleware(['auth.api'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');

    // Patients
    Route::get('/patients/table', [PatientController::class, 'table'])->name('patients.table');
    Route::resource('patients', PatientController::class);

    // Doctors
    Route::get('/doctors/table', [DoctorController::class, 'table'])->name('doctors.table');
    Route::get('/doctors/{doctor}/patients/table', [DoctorController::class, 'patientsTable'])->name('doctors.patients.table');
    Route::resource('doctors', DoctorController::class)->only(['index', 'create', 'store','show', 'edit', 'update']);

    // Analyses
    Route::get('/analyses/table', [AnalysisController::class, 'table'])->name('analyses.table');  
    Route::post('/analyses/category-analyse', [AnalysisController::class, 'storeCategory']);  
    Route::post('/analyses/sample-types', [AnalysisController::class, 'storeSampleType']);  
    Route::post('/analyses/units', [AnalysisController::class, 'storeUnit']);  
    Route::resource('analyses', AnalysisController::class);

    // Samples
    Route::resource('samples', SamplesController::class);
    Route::put('samples/{id}/status', [SamplesController::class, 'updateStatus'])->name('samples.updateStatus');
    
    // APIs for select2/autocomplete
    Route::get('api/search/patients', [SamplesController::class, 'searchPatients'])->name('api.search.patients');  
    Route::get('api/search/doctors', [SamplesController::class, 'searchDoctors'])->name('api.search.doctors');

    // Quotations
    Route::resource('quotations', QuotationController::class);
    Route::get('/quotations/table', [QuotationController::class, 'table'])->name('quotations.table');
    Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');

    //Queue mangement
    Route::get('/queues', [QueueController::class, 'index'])->name('queues.index');
    Route::post('/queues', [QueueController::class, 'store'])->name('queues.store');
    Route::delete('/queues/{id}', [QueueController::class, 'destroy'])->name('queues.destroy');
    Route::post('/queues/move-next', [QueueController::class, 'moveNext'])->name('queues.moveNext');
    Route::get('/queues/display', [QueueController::class, 'show'])->name('queues.show');

    // Agreements
    Route::resource('agreements', AgreementController::class);

    //reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// Routes only for ADMIN users
Route::middleware(['auth.api', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
    Route::get('admin/users/{id}', [UserController::class, 'show'])->name('admin.users.show');
    Route::get('admin/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');

});

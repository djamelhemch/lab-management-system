<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    PatientController,
    DoctorController,
    DashboardController,
    StatisticsController,
    AnalysisController,
    SamplesController,
    QuotationController,
    AuthController,
    Admin\UserController,
    Admin\LogsController,
    AgreementController,
    QueueController,
    ProfileController,
    LeaveRequestController,
    LabResultController,
    LabDeviceController
};

// Authentication routes
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes for authenticated users (admin + user)
Route::middleware(['auth.api'])->group(function () {
    
    // Dashboard (Hub)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Statistics (Old Dashboard)
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
    Route::get('/statistics/stats', [StatisticsController::class, 'stats'])->name('statistics.stats');

    // Profile page
    Route::get('/profile/{userId}', [ProfileController::class, 'show'])->name('profiles.show');
    Route::put('/profile/{userId}', [ProfileController::class, 'update'])->name('profiles.update');
    
    // Leave Requests
    Route::post('/leave-requests', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
    Route::delete('/leave-requests/{id}', [LeaveRequestController::class, 'destroy'])->name('leave-requests.destroy');

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
    
    // Lab devices
    Route::resource('lab-devices', LabDeviceController::class);
    
    // Samples
    Route::resource('samples', SamplesController::class);
    Route::put('samples/{id}/status', [SamplesController::class, 'updateStatus'])->name('samples.updateStatus');
    
    // APIs for select2/autocomplete
    Route::get('api/search/patients', [SamplesController::class, 'searchPatients'])->name('api.search.patients');  
    Route::get('api/search/doctors', [SamplesController::class, 'searchDoctors'])->name('api.search.doctors');

    // Quotations
    Route::get('/quotations/table', [QuotationController::class, 'table'])->name('quotations.table');
    Route::put('quotations/{id}/convert', [QuotationController::class, 'convert'])->name('quotations.convert');
    Route::get('quotations/{id}/download', [QuotationController::class, 'download'])->name('quotations.download');
    Route::post('/patients/ajax-store', [QuotationController::class, 'storePatient'])->name('patients.ajaxStore');
    Route::resource('quotations', QuotationController::class);

    // Queue management
    Route::get('/queues', [QueueController::class, 'index'])->name('queues.index');
    Route::post('/queues', [QueueController::class, 'store'])->name('queues.store');
    Route::delete('/queues/{id}', [QueueController::class, 'destroy'])->name('queues.destroy');
    Route::post('/queues/move-next', [QueueController::class, 'moveNext'])->name('queues.moveNext');
    Route::put('/queues/{id}/priority', [QueueController::class, 'updatePriority'])->name('queues.updatePriority');
    
    // Queue display screen (can be public or authenticated)
    Route::get('/queues/display', [QueueController::class, 'show'])->name('queues.show');
    
    // Queue status API endpoint
    Route::get('/api/queues/status', [QueueController::class, 'getQueueStatus'])->name('queues.status');
    // Agreements
    Route::resource('agreements', AgreementController::class);

    // Lab results
    Route::prefix('lab-results')->group(function () {
        Route::get('/', [LabResultController::class, 'index'])->name('lab-results.index');
        Route::get('/patient/{patientId}', [LabResultController::class, 'patientResults'])->name('lab-results.patient');
        Route::post('/', [LabResultController::class, 'store'])->name('lab-results.store');
        Route::get('/{id}', [LabResultController::class, 'show'])->name('lab-results.show');
        Route::get('/{id}/download', [LabResultController::class, 'download'])->name('lab-results.download');
    });  
});

// Routes only for ADMIN users
Route::middleware(['auth.api', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('users', UserController::class);
        Route::get('logs', [LogsController::class, 'index'])->name('logs');

        // Settings routes
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])
            ->name('settings.index');
        Route::post('/settings/{id}/options', [\App\Http\Controllers\Admin\SettingsController::class, 'addOption'])
            ->name('settings.addOption');
        Route::delete('/settings/options/{id}', [\App\Http\Controllers\Admin\SettingsController::class, 'deleteOption'])
            ->name('settings.deleteOption');
        Route::put('/settings/{id}/options/{optionId}/default', [\App\Http\Controllers\Admin\SettingsController::class, 'setDefault'])
            ->name('settings.setDefault');
    });

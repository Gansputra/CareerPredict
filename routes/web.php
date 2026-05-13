<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LandingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ExpertSystemController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\JobManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\JobImportController;

Route::get('/', [LandingController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Expert System
    Route::get('/expert', [ExpertSystemController::class, 'index'])->name('expert.index');
    Route::post('/expert/calculate', [ExpertSystemController::class, 'calculate'])->name('expert.calculate');
    Route::get('/expert/results', [ExpertSystemController::class, 'results'])->name('expert.results');

    // New Assessment System
    Route::get('/assessment', [AssessmentController::class, 'index'])->name('assessment.index');
    Route::post('/assessment', [AssessmentController::class, 'store'])->name('assessment.store');

    // Jobs
    Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{slug}', [JobController::class, 'show'])->name('jobs.show');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/jobs/import', [JobImportController::class, 'index'])->name('jobs.import');
    Route::post('/jobs/import', [JobImportController::class, 'import'])->name('jobs.import.post');
    Route::resource('jobs', JobManagementController::class);
    Route::resource('users', UserManagementController::class);
});

require __DIR__.'/auth.php';

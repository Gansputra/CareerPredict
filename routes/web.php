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
    Route::post('/assessment/reset', [AssessmentController::class, 'reset'])->name('assessment.reset');

    // CV Analyzer
    Route::get('/cv-analyzer', [App\Http\Controllers\CvAnalyzerController::class, 'index'])->name('cv.index');
    Route::post('/cv-analyzer/analyze', [App\Http\Controllers\CvAnalyzerController::class, 'analyze'])->name('cv.analyze');
    Route::post('/cv-analyzer/reset', [App\Http\Controllers\CvAnalyzerController::class, 'reset'])->name('cv.reset');
    Route::post('/cv-analyzer/save-category', [App\Http\Controllers\CvAnalyzerController::class, 'saveCategory'])->name('cv.saveCategory');

    // Jobs
    Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/search/json', [JobController::class, 'search'])->name('jobs.search');
    Route::get('/jobs/{slug}', [JobController::class, 'show'])->name('jobs.show');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Salary Insights
    Route::get('/salary', [App\Http\Controllers\SalaryInsightsController::class, 'index'])->name('salary.index');

    // Learning Roadmap
    Route::get('/roadmap', [App\Http\Controllers\RoadmapController::class, 'index'])->name('roadmap.index');

    // Job Market Trends
    Route::get('/market-trends', [App\Http\Controllers\MarketTrendsController::class, 'index'])->name('market.index');
    Route::get('/roadmap/{slug}', [App\Http\Controllers\RoadmapController::class, 'show'])->name('roadmap.show');

    // Skill Matrix
    Route::get('/skill-matrix', [App\Http\Controllers\SkillMatrixController::class, 'index'])->name('skillmatrix.index');

    // Application Tracker
    Route::get('/tracker', [App\Http\Controllers\ApplicationTrackerController::class, 'index'])->name('tracker.index');
    Route::post('/tracker', [App\Http\Controllers\ApplicationTrackerController::class, 'store'])->name('tracker.store');
    Route::patch('/tracker/{application}/status', [App\Http\Controllers\ApplicationTrackerController::class, 'updateStatus'])->name('tracker.updateStatus');
    Route::patch('/tracker/{application}/notes', [App\Http\Controllers\ApplicationTrackerController::class, 'updateNotes'])->name('tracker.updateNotes');
    Route::delete('/tracker/{application}', [App\Http\Controllers\ApplicationTrackerController::class, 'destroy'])->name('tracker.destroy');

    // Interview Simulator
    Route::get('/interview', [App\Http\Controllers\InterviewSimulatorController::class, 'index'])->name('interview.index');
    Route::get('/interview/{category}', [App\Http\Controllers\InterviewSimulatorController::class, 'show'])->name('interview.show');

    // Dokumentasi
    Route::get('/docs', fn() => view('docs.index'))->name('docs.index');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/jobs/import', [JobImportController::class, 'index'])->name('jobs.import');
    Route::post('/jobs/import/csv', [JobImportController::class, 'importCsv'])->name('jobs.import.csv');
    Route::post('/jobs/import/api', [JobImportController::class, 'importApi'])->name('jobs.import.api');
    Route::post('/jobs/clear', [JobImportController::class, 'clearJobs'])->name('jobs.clear');
    Route::post('/jobs/import', [JobImportController::class, 'importCsv'])->name('jobs.import.post');
    Route::resource('jobs', JobManagementController::class);
    Route::resource('users', UserManagementController::class);
    Route::resource('sponsors', \App\Http\Controllers\Admin\SponsorBannerController::class);
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

// Welcome Landing Page
Route::get('/', function () {
    return view('welcome');
});

// Authenticated Candidate Routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard Stats
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Interview Flow
    Route::get('/interviews/setup', [InterviewController::class, 'setup'])->name('interviews.setup');
    Route::post('/interviews/start', [InterviewController::class, 'start'])->name('interviews.start');
    Route::get('/interviews/{id}/arena', [InterviewController::class, 'arena'])->name('interviews.arena');
    Route::post('/interviews/{id}/save-answer', [InterviewController::class, 'saveAnswer'])->name('interviews.save-answer');
    Route::get('/interviews/{id}/submit', [InterviewController::class, 'submit'])->name('interviews.submit');
    Route::post('/interviews/{id}/trigger-evaluation', [InterviewController::class, 'triggerEvaluation'])->name('interviews.trigger-evaluation');
    
    // Result Reports
    Route::get('/results/{id}', [ResultController::class, 'show'])->name('results.show');
    Route::get('/results/{id}/export', [ResultController::class, 'exportCsv'])->name('results.export');

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Panel Routes (Protected by auth and role middleware)
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Category CRUD
    Route::resource('categories', CategoryController::class);
    
    // User Management
    Route::resource('users', UserController::class)->only(['index', 'edit', 'update', 'destroy']);
    
    // System Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LabDashboardController;
use App\Http\Controllers\LabTestController;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Lab test — managers and lab users
Route::middleware(['auth', 'portal:manager,lab'])->group(function () {
    Route::post('/entries/{id}/lab-test', [LabTestController::class, 'upsert'])->name('entries.lab-test');
});

// Lab portal
Route::middleware(['auth', 'lab'])->group(function () {
    Route::get('/lab', [LabDashboardController::class, 'index'])->name('lab.dashboard');
});

// Protected Admin Dashboard Routes
Route::middleware(['auth', 'manager'])->group(function () {
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/stats', [AdminDashboardController::class, 'getStatsJson'])->name('admin.stats.json');
    Route::post('/admin/entries/{id}/status', [AdminDashboardController::class, 'updateStatus'])->name('admin.entries.status');
    Route::post('/admin/units', [AdminDashboardController::class, 'storeUnit'])->name('admin.units.store');
    Route::post('/admin/supervisors', [AdminDashboardController::class, 'storeSupervisor'])->name('admin.supervisors.store');
    Route::post('/admin/settings/quality', [AdminDashboardController::class, 'updateQualitySettings'])->name('admin.settings.quality');
    Route::post('/admin/settings/profile', [AdminDashboardController::class, 'updateProfile'])->name('admin.settings.profile');
});


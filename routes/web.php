<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Admin Dashboard Routes
Route::middleware(['auth', 'manager'])->group(function () {
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/entries/{id}/status', [AdminDashboardController::class, 'updateStatus'])->name('admin.entries.status');
});


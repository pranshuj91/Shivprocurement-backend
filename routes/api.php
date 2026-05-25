<?php

use App\Http\Controllers\UnloadingApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Procurement API Endpoints
Route::post('/login', [UnloadingApiController::class, 'login']);
Route::post('/signup', [UnloadingApiController::class, 'signup']);

// Protected Procurement API Endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/units', [UnloadingApiController::class, 'getUnits']);
    Route::get('/suppliers', [UnloadingApiController::class, 'getSuppliers']);
    Route::get('/entries', [UnloadingApiController::class, 'getEntries']);
    Route::post('/entries', [UnloadingApiController::class, 'storeEntry']);
});


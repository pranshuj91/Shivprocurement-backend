<?php

use App\Http\Controllers\UnloadingApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Procurement API Endpoints
Route::post('/login', [UnloadingApiController::class, 'login']);
Route::get('/units', [UnloadingApiController::class, 'getUnits']);
Route::get('/suppliers', [UnloadingApiController::class, 'getSuppliers']);
Route::get('/entries', [UnloadingApiController::class, 'getEntries']);
Route::post('/entries', [UnloadingApiController::class, 'storeEntry']);


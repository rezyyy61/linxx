<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PoliticalProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);

    Route::get('/political-profiles/me', [PoliticalProfileController::class, 'me']);
    Route::apiResource('political-profiles', PoliticalProfileController::class);


});

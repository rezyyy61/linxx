<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PoliticalProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/political-profiles', [PoliticalProfileController::class, 'index']);
Route::get('/political-profiles/{politicalProfile}', [PoliticalProfileController::class, 'show'])
    ->where('politicalProfile', '[0-9]+');;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);

    Route::get('/political-profiles/me', [PoliticalProfileController::class, 'me']);
    Route::post('/political-profiles', [PoliticalProfileController::class, 'store']);
    Route::put('/political-profiles/{politicalProfile}', [PoliticalProfileController::class, 'update']);
    Route::delete('/political-profiles/{politicalProfile}', [PoliticalProfileController::class, 'destroy']);
});

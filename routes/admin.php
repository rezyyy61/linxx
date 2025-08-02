<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\TwoFactorLoginController;
use App\Http\Controllers\Admin\Auth\TwoFactorController;


Route::post('/login', [LoginController::class, 'login']);
Route::post('/2fa/setup', [TwoFactorLoginController::class, 'setup']);
Route::post('/verify-2fa-login', [TwoFactorLoginController::class, 'verify']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable']);
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify']);
//
//    Route::get('/dashboard', [DashboardController::class, 'index']);
});

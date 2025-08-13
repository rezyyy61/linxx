<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\TwoFactorController;
use App\Http\Controllers\Admin\Auth\TwoFactorLoginController;
use App\Http\Controllers\Admin\Book\BookController;
use App\Http\Controllers\Admin\Book\SuggestedBookController;
use App\Http\Controllers\Admin\Users\UserController;
use App\Http\Controllers\Admin\Users\UserTypeRequestController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [LoginController::class, 'login']);
Route::post('/2fa/setup', [TwoFactorLoginController::class, 'setup']);
Route::post('/verify-2fa-login', [TwoFactorLoginController::class, 'verify']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable']);
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify']);
//
//    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('suggested-books',                 [SuggestedBookController::class, 'index']);
    Route::get('suggested-books/{suggested}',     [SuggestedBookController::class, 'show']);
    Route::put('suggested-books/{suggested}',     [SuggestedBookController::class, 'update']);
    Route::post('suggested-books/{suggested}/approve', [SuggestedBookController::class, 'approve']);
    Route::delete('suggested-books/{suggested}',  [SuggestedBookController::class, 'destroy']);

    Route::get('/book-categories', [BookController::class, 'getCategories']);
    Route::get('/books', [BookController::class, 'index']);
    Route::post('/books', [BookController::class, 'store']);
    Route::get('/books/{book}', [BookController::class, 'show']);
    Route::put('/books/{book}', [BookController::class, 'update']);
    Route::delete('/books/{book}', [BookController::class, 'destroy']);

    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    Route::get('user-type-requests', [UserTypeRequestController::class, 'index']);
    Route::post('users/{id}/type-requests/approve', [UserTypeRequestController::class, 'approve']);
    Route::post('users/{id}/type-requests/reject', [UserTypeRequestController::class, 'reject']);


});


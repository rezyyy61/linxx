<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\MediaDownloadController;
use App\Http\Controllers\Api\PoliticalProfileController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Middleware\OptionalAuth;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/political-profiles', [PoliticalProfileController::class, 'index']);
Route::get('/political-profiles/{politicalProfile}', [PoliticalProfileController::class, 'show'])
    ->where('politicalProfile', '[0-9]+');
Route::get('/parties/{id}/publications', [PublicationController::class, 'listByParty']);
Route::get('/posts', [PostController::class, 'index']);
Route::get('/media/download/{id}', [MediaDownloadController::class, 'show'])
    ->name('media.download')
    ->whereNumber('id');
Route::get('/comments', [CommentController::class, 'index'])
    ->middleware(OptionalAuth::class);




Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);

    Route::get('/political-profiles/me', [PoliticalProfileController::class, 'me']);
    Route::post('/political-profiles', [PoliticalProfileController::class, 'store']);
    Route::put('/political-profiles/{politicalProfile}', [PoliticalProfileController::class, 'update']);
    Route::delete('/political-profiles/{politicalProfile}', [PoliticalProfileController::class, 'destroy']);

    Route::get('/publications', [PublicationController::class, 'index']);
    Route::post('/publications', [PublicationController::class, 'store']);
    Route::delete('/publications/{publication}', [PublicationController::class, 'destroy']);
    Route::post('/publications/suggest-description', [PublicationController::class, 'suggestDescription']);

    Route::post('/posts', [PostController::class, 'store']);

    Route::post('/comments', [CommentController::class, 'store']);
    Route::post('/comments/{comment}/like', [CommentController::class, 'like']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    Route::post('/comments/{comment}/report', [CommentController::class, 'report']);
});

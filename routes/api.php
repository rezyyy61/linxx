<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Book\BookSuggestionController;
use App\Http\Controllers\Api\Book\CategoryController;
use App\Http\Controllers\Api\Book\PublicBookController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\MediaDownloadController;
use App\Http\Controllers\Api\PoliticalProfileController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Controllers\Api\VideoPostController;
use App\Http\Middleware\OptionalAuth;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/political-profiles', [PoliticalProfileController::class, 'index']);


Route::get('/political-profiles/{politicalProfile}', [PoliticalProfileController::class, 'show'])
    ->where('politicalProfile', '[0-9]+');

Route::get('/posts', [PostController::class, 'index']);


Route::get('/media/download/{id}', [MediaDownloadController::class, 'show'])
    ->name('media.download')
    ->whereNumber('id');


Route::get('/comments', [CommentController::class, 'index'])
    ->middleware(OptionalAuth::class);

Route::get('/political-profiles/user/{slug}', [PoliticalProfileController::class, 'showBySlug']);


Route::get('/political-profiles/user/{slug}/posts', [PoliticalProfileController::class, 'postsByProfileSlug']);

Route::get('/videos', [VideoPostController::class, 'index']);
Route::get('/videos/{id}', [VideoPostController::class, 'showVideoById']);

Route::get('/posts/{post}/likes-preview', [LikeController::class, 'preview']);

Route::get('/books/search', [PublicBookController::class, 'search']);
Route::get('/books', [PublicBookController::class, 'index']);
Route::get('/books/{slug}/download', [PublicBookController::class, 'download'])->name('books.download');
Route::get('/books/{slug}', [PublicBookController::class, 'show']);
Route::get('/books/{book}/reviews', [PublicBookController::class, 'reviews']);
Route::get('/books/{book}/read', [PublicBookController::class, 'stream'])->name('books.stream');
Route::get('/categories', [CategoryController::class, 'index']);




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

    Route::post('/posts/{post}/like', [LikeController::class, 'toggle']);

    Route::post('/books/{book}/reviews', [PublicBookController::class, 'submitReview']);
    Route::post('/books/suggestions', [BookSuggestionController::class, 'store']);


});

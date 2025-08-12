<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Book\BookSuggestionController;
use App\Http\Controllers\Api\Book\CategoryController;
use App\Http\Controllers\Api\Book\PublicBookController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\Event\EventController;
use App\Http\Controllers\Api\Event\EventInviteController;
use App\Http\Controllers\Api\Event\EventRsvpController;
use App\Http\Controllers\Api\Event\Public\PublicEventController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\MediaDownloadController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\Profile\PoliticalProfileController;
use App\Http\Controllers\Api\Profile\PublicProfileController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Controllers\Api\Share\ReshareController;
use App\Http\Controllers\Api\Share\RevokeShareController;
use App\Http\Controllers\Api\Share\ShowShareController;
use App\Http\Controllers\Api\Share\StoreShareController;
use App\Http\Controllers\Api\VideoPostController;
use App\Http\Middleware\OptionalAuth;
use Illuminate\Support\Facades\Route;

// ------------------------ AUTH ------------------------
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('resend-code', [AuthController::class, 'resendCode']);
});

// ------------------------ PROFILE ------------------------
Route::prefix('profile')->group(function () {
    Route::get('/', [PublicProfileController::class, 'index']);
    Route::get('parties', [PublicProfileController::class, 'index'])->defaults('types', 'party');
    Route::get('individuals', [PublicProfileController::class, 'index'])->defaults('group', 'individuals');

    Route::get('slug/{slug}', [PublicProfileController::class, 'showProfile']);
    Route::get('slug/{slug}/posts', [PublicProfileController::class, 'getPosts']);
    Route::get('slug/{slug}/posts/images', [PublicProfileController::class, 'getImagePosts']);
    Route::get('slug/{slug}/posts/videos', [PublicProfileController::class, 'getVideoPosts']);
    Route::get('slug/{slug}/posts/files', [PublicProfileController::class, 'getFilePosts']);
});


// ------------------------ POSTS ------------------------
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::get('/posts/{post}/likes-preview', [LikeController::class, 'preview']);

// ------------------------ VIDEOS ------------------------
Route::get('/videos', [VideoPostController::class, 'index']);
Route::get('/videos/{id}', [VideoPostController::class, 'showVideoById']);

// ------------------------ BOOKS ------------------------
Route::prefix('books')->group(function () {
    Route::get('/', [PublicBookController::class, 'index']);
    Route::get('search', [PublicBookController::class, 'search']);
    Route::get('{slug}/download', [PublicBookController::class, 'download'])->name('books.download');
    Route::get('{slug}', [PublicBookController::class, 'show']);
    Route::get('{book}/reviews', [PublicBookController::class, 'reviews']);
    Route::get('{book}/read', [PublicBookController::class, 'stream'])->name('books.stream');
});

// ------------------------ CATEGORIES ------------------------
Route::get('/categories', [CategoryController::class, 'index']);

// ------------------------ COMMENTS ------------------------
Route::get('/comments', [CommentController::class, 'index'])->middleware(OptionalAuth::class);

// ------------------------ MEDIA ------------------------
Route::get('/media/download/{id}', [MediaDownloadController::class, 'show'])
    ->name('media.download')
    ->whereNumber('id');

// ------------------------ EVENT ------------------------
Route::get('public/events', [PublicEventController::class, 'index']);
Route::get('public/events/{idOrSlug}', [PublicEventController::class, 'show']);
Route::get('events/{event}/attendees', [EventRsvpController::class, 'attendees']);

// ------------------------ AUTH REQUIRED ------------------------
Route::middleware('auth:sanctum')->group(function () {

    // AUTH
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);

    // POLITICAL PROFILE
    Route::prefix('political-profiles')->group(function () {
        Route::get('me', [PoliticalProfileController::class, 'me']);
        Route::post('/', [PoliticalProfileController::class, 'storeOrUpdate']);
        Route::delete('{politicalProfile}', [PoliticalProfileController::class, 'destroy']);
    });

    // PUBLICATIONS
    Route::prefix('publications')->group(function () {
        Route::get('/', [PublicationController::class, 'index']);
        Route::post('/', [PublicationController::class, 'store']);
        Route::delete('{publication}', [PublicationController::class, 'destroy']);
        Route::post('suggest-description', [PublicationController::class, 'suggestDescription']);
    });

    // POSTS
    Route::post('/posts', [PostController::class, 'store']);
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle']);
    Route::post('/reshare', ReshareController::class)->name('shares.reshare');

    // COMMENTS
    Route::prefix('comments')->group(function () {
        Route::post('/', [CommentController::class, 'store']);
        Route::put('{comment}', [CommentController::class, 'update']);
        Route::delete('{comment}', [CommentController::class, 'destroy']);
        Route::post('{comment}/like', [CommentController::class, 'like']);
        Route::post('{comment}/report', [CommentController::class, 'report']);
    });

    // BOOKS
    Route::post('/books/{book}/reviews', [PublicBookController::class, 'submitReview']);
    Route::post('/books/suggestions', [BookSuggestionController::class, 'store']);


    //EVENTS
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{event}', [EventController::class, 'show']);
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::patch('/events/{event}/status', [EventController::class, 'updateStatus'])
        ->name('events.status');
    Route::delete('/events/{event}', [EventController::class, 'destroy']);

    Route::post('events/{event}/rsvp', [EventRsvpController::class, 'store']);
    Route::delete('events/{event}/rsvp', [EventRsvpController::class, 'destroy']);

    Route::get('/events/{event}/invites', [EventInviteController::class, 'index']);
    Route::post('/events/{event}/invites', [EventInviteController::class, 'store']);
    Route::patch('/event-invites/{invite}', [EventInviteController::class, 'respond']);
    Route::delete('/event-invites/{invite}', [EventInviteController::class, 'destroy']);

    //SHARES
    Route::post('/shares', StoreShareController::class)->name('shares.store');
    Route::get('/shares/{slug}', ShowShareController::class)->name('shares.show');
    Route::post('/shares/{id}/revoke', RevokeShareController::class)->name('shares.revoke');

});

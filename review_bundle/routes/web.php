<?php

use App\Http\Controllers\Api\Auth\SocialAuthController;
use App\Http\Controllers\Share\ShareRedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Route::prefix('auth')->group(function () {
    Route::get('{provider}', [SocialAuthController::class, 'redirect']);
    Route::get('{provider}/callback', [SocialAuthController::class, 'callback']);
});



Route::get('/r/{slug}', [ShareRedirectController::class, 'show'])->name('share.resolve');
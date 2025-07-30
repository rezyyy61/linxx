<?php

use App\Http\Controllers\ShareController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/share/posts/{id}', [ShareController::class, 'show'])->name('share.post');

Route::get('/test-share', function () {
    return 'Route is working!';
});

Route::get('/test-share/{id}', function ($id) {
    $post = \App\Models\Post::with('media')->findOrFail($id);

    $image = $post->media->firstWhere('type', 'image')?->full_url
        ?? $post->media->firstWhere('type', 'video')?->full_poster
        ?? asset('images/default-post.jpg');

    return [
        'title' => $post->title,
        'text' => $post->text,
        'image' => $image,
    ];
});

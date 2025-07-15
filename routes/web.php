<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-broadcast', function () {
    broadcast(new \App\Events\TestMessage('پیام تست از سرور!'));
    return 'Event broadcasted!';
});

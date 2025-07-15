<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Broadcast::channel('user.{id}', function ($authUser, $id) {
    return (int) $authUser->id === (int) $id;
});

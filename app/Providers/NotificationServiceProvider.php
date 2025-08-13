<?php

namespace App\Providers;

use App\Support\Notifications\Notifier;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('app.notifier', function () {
            return new Notifier();
        });
    }

    public function boot(): void
    {
    }
}

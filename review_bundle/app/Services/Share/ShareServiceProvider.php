<?php

namespace App\Services\Share;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ShareServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public $bindings = [
        \App\Services\Share\Contracts\ShareCreator::class => \App\Services\Share\Implementations\EloquentShareCreator::class,
        \App\Services\Share\Contracts\ShareResolver::class => \App\Services\Share\Implementations\EloquentShareResolver::class,
        \App\Services\Share\Contracts\ClickRegistrar::class => \App\Services\Share\Implementations\EloquentClickRegistrar::class,
        \App\Services\Share\Contracts\ShareRevoker::class => \App\Services\Share\Implementations\EloquentShareRevoker::class,
        \App\Services\Share\Contracts\AnalyticsReporter::class => \App\Services\Share\Implementations\EloquentAnalyticsReporter::class,
    ];

    public $singletons = [
        \App\Services\Share\Contracts\SlugGenerator::class => \App\Services\Share\Implementations\RandomSlugGenerator::class,
        \App\Services\Share\Contracts\LinkBuilder::class => \App\Services\Share\Implementations\DefaultLinkBuilder::class,
        \App\Services\Share\Contracts\PermissionGate::class => \App\Services\Share\Implementations\DefaultPermissionGate::class,
        \App\Services\Share\Contracts\WebhookPusher::class => \App\Services\Share\Implementations\HttpWebhookPusher::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }

    public function provides(): array
    {
        return array_merge(
            array_keys($this->bindings),
            array_keys($this->singletons)
        );
    }
}

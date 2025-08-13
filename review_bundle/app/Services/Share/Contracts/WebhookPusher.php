<?php

namespace App\Services\Share\Contracts;


use App\Models\Share\Share;

interface WebhookPusher
{
    public function push(string $event, Share $share, array $payload = []): void;
}

<?php

namespace App\Services\Share\Implementations;

use App\Models\Share\Share;
use App\Services\Share\Contracts\ShareRevoker;
use App\Services\Share\Contracts\WebhookPusher;
use Illuminate\Support\Carbon;

class EloquentShareRevoker implements ShareRevoker
{
    public function __construct(protected WebhookPusher $webhooks) {}

    public function revoke(Share $share): void
    {
        if ($share->revoked_at) {
            return;
        }
        $share->revoked_at = Carbon::now();
        $share->save();
        $this->webhooks->push('shares.revoked', $share);
    }
}

<?php

namespace App\Services\Share\Implementations;

use App\Models\Share\Share;
use App\Services\Share\Contracts\ShareResolver;
use App\Services\Share\Enums\ShareResolutionStatus;
use App\Services\Share\Results\ShareResolution;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class EloquentShareResolver implements ShareResolver
{
    public function resolveBySlug(string $slug, ?string $password = null): ShareResolution
    {
        $share = Share::query()->where('slug', $slug)->first();

        if (!$share) {
            return new ShareResolution(ShareResolutionStatus::NOT_FOUND);
        }

        if ($share->revoked_at !== null) {
            return new ShareResolution(ShareResolutionStatus::REVOKED, $share);
        }

        if ($share->expires_at && Carbon::now()->greaterThan($share->expires_at)) {
            return new ShareResolution(ShareResolutionStatus::EXPIRED, $share);
        }

        if ($share->password_hash && (!$password || !Hash::check($password, $share->password_hash))) {
            return new ShareResolution(ShareResolutionStatus::FORBIDDEN, $share);
        }

        if ($share->max_clicks !== null && $share->clicks_count >= $share->max_clicks) {
            return new ShareResolution(ShareResolutionStatus::EXPIRED, $share);
        }

        return new ShareResolution(ShareResolutionStatus::OK, $share);
    }
}

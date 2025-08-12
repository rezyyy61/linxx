<?php

namespace App\Http\Controllers\Api\Share;

use App\Http\Controllers\Controller;
use App\Models\Share\Share;
use Illuminate\Http\JsonResponse;

class ShowShareController extends Controller
{
    public function __invoke(string $slug): JsonResponse
    {
        $share = Share::query()->where('slug', $slug)->firstOrFail();

        return response()->json([
            'id' => $share->id,
            'slug' => $share->slug,
            'scope' => $share->scope,
            'expires_at' => optional($share->expires_at)?->toISOString(),
            'revoked_at' => optional($share->revoked_at)?->toISOString(),
            'clicks_count' => $share->clicks_count,
            'shareable' => [
                'type' => $share->shareable_type,
                'id' => $share->shareable_id,
            ],
        ]);
    }
}

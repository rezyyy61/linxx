<?php

namespace App\Http\Controllers\Api\Share;

use App\Http\Controllers\Controller;
use App\Http\Requests\Share\StoreShareRequest;
use App\Services\Share\Contracts\ShareCreator;
use App\Services\Share\Contracts\LinkBuilder;
use App\Services\Share\DTOs\CreateShareData;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class StoreShareController extends Controller
{
    public function __invoke(StoreShareRequest $request, ShareCreator $creator, LinkBuilder $links): JsonResponse
    {
        $type = $request->string('shareable_type')->toString();
        $morphMap = Relation::getMorphedModel($type) ?: $type;
        $model = app($morphMap)::query()->findOrFail((int)$request->input('shareable_id'));

        $share = $creator->create(new CreateShareData(
            actor: $request->user(),
            shareable: $model,
            scope: $request->string('scope')->toString(),
            expiresAt: $request->filled('expires_at') ? Carbon::parse($request->input('expires_at')) : null,
            maxClicks: $request->input('max_clicks'),
            allowRepost: (bool)$request->input('allow_repost', true),
            password: $request->input('password'),
            extra: (array)$request->input('extra', [])
        ));

        return response()->json([
            'id' => $share->id,
            'slug' => $share->slug,
            'link' => $links->build($share),
            'scope' => $share->scope,
            'expires_at' => optional($share->expires_at)?->toISOString(),
        ], 201);
    }
}

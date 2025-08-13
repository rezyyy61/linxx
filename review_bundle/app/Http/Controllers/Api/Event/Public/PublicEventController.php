<?php

namespace App\Http\Controllers\Api\Event\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\Public\EventPublicIndexRequest;
use App\Http\Resources\Event\Public\EventPublicResource;
use App\Services\Event\EventQueryService;

class PublicEventController extends Controller
{
    public function __construct(private EventQueryService $service) {}

    public function index(EventPublicIndexRequest $request)
    {
        $list = $this->service->listPublic($request->validated());
        return EventPublicResource::collection($list);
    }

    public function show(string $idOrSlug)
    {
        $event = $this->service->findPublic($idOrSlug);
        return new EventPublicResource($event);
    }
}

<?php

namespace App\Http\Controllers\Api\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\EventStoreRequest;
use App\Http\Requests\Event\EventUpdateRequest;
use App\Http\Resources\Event\EventResource;
use App\Http\Resources\Event\EventSummaryResource;
use App\Models\Event\Event;
use App\Services\Event\EventService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function __construct(private EventService $service) {}

    public function index(Request $request)
    {
        $query = Event::query();

        $query->orderBy('starts_at');

        $events = $query->with(['creator'])->paginate($request->integer('per_page', 15));

        return EventSummaryResource::collection($events);
    }

    public function show(Event $event)
    {
        $event->load(['creator', 'createdBy', 'rsvps.user', 'invites.user', 'invites.invitedBy']);
        return new EventResource($event);
    }


    public function store(EventStoreRequest $request)
    {
        $data = $request->validated();
        $data['created_by_user_id'] = $data['created_by_user_id'] ?? $request->user()->id;

        $event = $this->service->create(
            $data,
            $request->file('cover'),
            $request->file('attachment')
        );

        return (new EventResource($event->load(['creator', 'createdBy'])))
            ->response()->setStatusCode(201);
    }

    public function update(EventUpdateRequest $request, Event $event)
    {
        $event = $this->service->update(
            $event,
            $request->validated(),
            $request->file('cover'),
            $request->file('attachment')
        );

        return new EventResource($event->fresh()->load(['creator', 'createdBy']));
    }

    public function updateStatus(Request $request, Event $event)
    {

        $data = $request->validate([
            'status' => ['required', Rule::in(['draft','published','cancelled','archived'])],
        ]);

        $event->update(['status' => $data['status']]);

        return new EventResource($event->fresh()->load(['creator','createdBy']));
    }
    public function destroy(Event $event)
    {
        $this->service->delete($event);
        return response()->noContent();
    }
}

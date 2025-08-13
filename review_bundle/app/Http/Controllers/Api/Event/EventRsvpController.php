<?php

namespace App\Http\Controllers\Api\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\EventRsvpRequest;
use App\Http\Resources\Event\EventRsvpResource;
use App\Models\Event\Event;
use App\Models\Event\EventRsvp;
use App\Services\Event\EventRsvpService;
use Illuminate\Http\Request;

class EventRsvpController extends Controller
{
    public function __construct(private EventRsvpService $service) {}

    public function store(EventRsvpRequest $request, Event $event)
    {
        $user = $request->user();
        $status = $request->validated('status'); // going | interested

        $rsvp = $this->service->rsvp($event, $user, $status);

        return (new EventRsvpResource($rsvp->load('user')))
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Request $request, Event $event)
    {
        $user = $request->user();

        $this->service->cancel($event, $user);

        return response()->noContent();
    }

    public function attendees(Event $event, Request $request)
    {
        $list = EventRsvp::with('user.politicalProfile')
            ->where('event_id', $event->id)
            ->get();

        return EventRsvpResource::collection($list);
    }
}

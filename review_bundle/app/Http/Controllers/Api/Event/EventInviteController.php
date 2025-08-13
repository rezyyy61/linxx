<?php

namespace App\Http\Controllers\Api\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\EventInviteRespondRequest;
use App\Http\Requests\Event\EventInviteStoreRequest;
use App\Http\Resources\Event\EventInviteResource;
use App\Models\Event\Event;
use App\Models\Event\EventInvite;
use App\Models\User;
use App\Services\Event\EventInviteService;


class EventInviteController extends Controller
{
    public function __construct(private EventInviteService $service) {}

    public function store(EventInviteStoreRequest $request, Event $event)
    {
        $invitee = User::findOrFail($request->integer('user_id'));
        $invite  = $this->service->invite($event, $invitee, $request->user());

        return (new EventInviteResource($invite->load(['user','invitedBy'])))
            ->response()->setStatusCode(201);
    }

    public function respond(EventInviteRespondRequest $request, EventInvite $invite)
    {
        $status = $request->string('status');

        if ($status === 'accepted') {
            $this->service->accept($invite);
        } elseif ($status === 'declined') {
            $this->service->reject($invite);
        } else {
            $invite->update(['status' => $status]);
        }

        return new EventInviteResource($invite->fresh()->load(['user','invitedBy']));
    }

    public function index(Event $event)
    {
        $invites = $event->invites()->with(['user','invitedBy'])->latest()->paginate(20);
        return EventInviteResource::collection($invites);
    }

    public function destroy(EventInvite $invite)
    {
        $invite->delete();
        return response()->noContent();
    }
}

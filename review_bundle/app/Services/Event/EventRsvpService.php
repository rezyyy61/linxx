<?php


namespace App\Services\Event;


use App\Models\Event\Event;
use App\Models\Event\EventRsvp;
use App\Models\User;

class EventRsvpService
{
    public function rsvp(Event $event, User $user, string $status): EventRsvp
    {
        return EventRsvp::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => $user->id],
            ['status' => $status]
        );
    }

    public function cancel(Event $event, User $user): bool
    {
        return EventRsvp::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->delete();
    }

    public function getAttendees(Event $event)
    {
        return $event->attendees()->get();
    }
}

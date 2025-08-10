<?php

namespace App\Services\Event;


use App\Models\Event\Event;
use App\Models\Event\EventInvite;
use App\Models\User;
use Illuminate\Support\Str;

class EventInviteService
{
    public function invite(Event $event, User $user, User $inviter): EventInvite
    {
        return EventInvite::create([
            'event_id'   => $event->id,
            'user_id'    => $user->id,
            'invited_by' => $inviter->id,
            'status'     => 'pending',
            'token'      => Str::uuid()
        ]);
    }

    public function accept(EventInvite $invite): bool
    {
        return $invite->update(['status' => 'accepted']);
    }

    public function reject(EventInvite $invite): bool
    {
        return $invite->update(['status' => 'rejected']);
    }
}

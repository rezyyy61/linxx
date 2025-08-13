<?php

namespace App\Events\Notifications;

use App\Models\Notifications\UserNotification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserNotificationBroadcasted implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public UserNotification $notification)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('App.Models.User.' . $this->notification->user_id)];
    }

    public function broadcastAs(): string
    {
        return 'notifications.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'title' => $this->notification->title,
            'body' => $this->notification->body,
            'action_url' => $this->notification->action_url,
            'subject_type' => $this->notification->subject_type,
            'subject_id' => $this->notification->subject_id,
            'count' => $this->notification->count,
            'actors' => $this->notification->actors ?: [],
            'level' => $this->notification->level,
            'created_at' => optional($this->notification->created_at)->toIso8601String(),
        ];
    }
}

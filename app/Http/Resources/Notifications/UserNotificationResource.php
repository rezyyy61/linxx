<?php

namespace App\Http\Resources\Notifications;

use Illuminate\Http\Resources\Json\JsonResource;

class UserNotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (string) $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'body' => $this->body,
            'action_url' => $this->action_url,
            'subject_type' => $this->subject_type,
            'subject_id' => $this->subject_id,
            'group_key' => $this->group_key,
            'actors' => $this->actors ?: [],
            'count' => (int) $this->count,
            'level' => $this->level,
            'seen_at' => optional($this->seen_at)->toIso8601String(),
            'read_at' => optional($this->read_at)->toIso8601String(),
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}

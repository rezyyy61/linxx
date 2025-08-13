<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\UserMiniResource;

class EventInviteResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'user'       => new UserMiniResource($this->whenLoaded('user', $this->user)),
            'status'     => $this->status,
            'invited_by' => new UserMiniResource($this->whenLoaded('invitedBy', $this->invitedBy)),
            'created_at' => $this->created_at,
        ];
    }
}

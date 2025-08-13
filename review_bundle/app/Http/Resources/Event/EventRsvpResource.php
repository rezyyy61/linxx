<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Resources\Json\JsonResource;

class EventRsvpResource extends JsonResource
{
    public function toArray($request): array
    {
        $u = $this->whenLoaded('user', $this->user);

        return [
            'user' => [
                'id'           => $u?->id,
                'name'         => $u?->name,
                'slug'         => $u?->slug,
                'avatar_color' => optional($u?->politicalProfile)->avatar_color,
                'logo_url'     => optional($u?->politicalProfile)->logo_url,
                'is_current'   => optional($request->user())->id === ($this->user_id ?? $u?->id),
            ],
            'status'        => (string) $this->status,
            'checked_in_at' => optional($this->checked_in_at)->toIso8601String(),
            'notes'         => $this->notes,
        ];
    }
}

<?php

namespace App\Http\Resources\Compact;

use Illuminate\Http\Resources\Json\JsonResource;

class EventCompactResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'slug'      => (string)($this->slug ?? $this->id),
            'title'     => $this->title,
            'description'=> $this->description,
            'city'      => $this->city,
            'cover_url' => $this->cover_url ? asset('storage/'.$this->cover_url) : null,
            'attachment_url'   => $this->attachment_url ? asset('storage/'.$this->attachment_url) : null,
            'starts_at' => optional($this->starts_at)->toIso8601String(),
            'ends_at'   => optional($this->ends_at)->toIso8601String(),
            'user'      => [
                'id'           => $this->user->id,
                'name'         => $this->user->name,
                'slug'         => $this->user->slug,
                'avatar_color' => optional($this->user->politicalProfile)->avatar_color,
                'logo_url'     => optional($this->user->politicalProfile)?->logo_url,
            ],
        ];
    }
}

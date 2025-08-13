<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\UserMiniResource;

class EventSummaryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'slug'        => $this->slug,
            'title'       => $this->title,
            'cover'       => $this->cover_url,
            'attachment_url'    => $this->attachment_url,
            'attachment_name'   => $this->attachment_path ? basename($this->attachment_path) : null,
            'starts_at'   => $this->starts_at,
            'city'        => $this->city,
            'visibility'  => $this->visibility,
            'status'      => $this->status,
            'creator'     => new UserMiniResource($this->whenLoaded('creator', $this->creator)),
            'going_count' => $this->going_count,
            'interested_count' => $this->interested_count,
        ];
    }
}

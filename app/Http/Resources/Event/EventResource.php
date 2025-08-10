<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\UserMiniResource;

class EventResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'slug'             => $this->slug,
            'title'            => $this->title,
            'description'      => $this->description,
            'cover_image_url'  => $this->cover_image_url,

            'type'             => $this->type,
            'mode'             => $this->mode,
            'visibility'       => $this->visibility,
            'status'           => $this->status,
            'allow_comments'   => (bool) $this->allow_comments,

            'timezone'         => $this->timezone,
            'starts_at'        => $this->starts_at,
            'ends_at'          => $this->ends_at,
            'rsvp_deadline'    => $this->rsvp_deadline,

            'capacity'         => $this->capacity,
            'requires_approval'=> (bool) $this->requires_approval,

            'venue_name'       => $this->venue_name,
            'address'          => $this->address,
            'city'             => $this->city,
            'country'          => $this->country,
            'lat'              => $this->lat,
            'lng'              => $this->lng,
            'platform'     => $this->platform,
            'stream_url'   => $this->stream_url,
            'meeting_link' => $this->meeting_link,
            'access_code'  => $this->access_code,

            'going_count'      => $this->going_count,
            'interested_count' => $this->interested_count,

            'creator'          => new UserMiniResource($this->whenLoaded('creator', $this->creator)),
            'created_by'       => new UserMiniResource($this->whenLoaded('createdBy', $this->createdBy)),

            'rsvps'            => EventRsvpResource::collection($this->whenLoaded('rsvps')),
            'invites'          => EventInviteResource::collection($this->whenLoaded('invites')),

            'cover_url'        => $this->cover_url,
            'attachment_url'   => $this->attachment_url,

            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}

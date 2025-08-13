<?php

namespace App\Http\Resources\Admin\Users;

use Illuminate\Http\Resources\Json\JsonResource;

class UserTypeRequestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
                'slug' => $this->user?->slug,
                'uuid' => $this->user?->uuid,
            ],
            'current_type' => $this->entity_type,
            'requested_type' => $this->pending_entity_type,
            'entity_type_approved' => (bool)$this->entity_type_approved,
            'group_name' => $this->group_name,
            'location' => $this->location,
            'avatar_color' => $this->avatar_color,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}

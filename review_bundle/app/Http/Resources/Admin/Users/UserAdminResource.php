<?php

namespace App\Http\Resources\Admin\Users;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAdminResource extends JsonResource
{
    public function toArray($request)
    {
        $p = $this->whenLoaded('politicalProfile');

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'slug' => $this->slug,
            'is_verified' => (bool)($this->is_verified ?? (bool)$this->email_verified_at),
            'verification_code' => $this->verification_code,
            'verification_expires_at' => optional($this->verification_expires_at)->toIso8601String(),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),

            'profile' => $p ? [
                'id' => $p->id,
                'user_id' => $p->user_id,
                'group_name' => $p->group_name,
                'tagline' => $p->tagline,
                'entity_type' => $p->entity_type,
                'pending_entity_type' => $p->pending_entity_type,
                'entity_type_approved' => (bool)$p->entity_type_approved,
                'location' => $p->location,
                'founded_year' => $p->founded_year,
                'logo_url' => $p && $p->logo_path
                    ? (preg_match('/^https?:\/\//', $p->logo_path)
                        ? $p->logo_path
                        : asset('storage/' . $p->logo_path))
                    : ($this->avatar ?? null),
                'about' => $p->about,
                'goals' => $p->goals,
                'activities' => $p->activities,
                'structure' => $p->structure,
                'avatar_color' => $p->avatar_color,
                'created_at' => optional($p->created_at)->toIso8601String(),
                'updated_at' => optional($p->updated_at)->toIso8601String(),
            ] : null,
        ];
    }
}

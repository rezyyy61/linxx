<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserMiniResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'slug'          => $this->slug,
            'name'          => $this->name,
            'email'         => $this->when(auth()->user()?->isAdmin(), $this->email),
            'avatar'        => $this->avatar,
            'avatar_color'  => $this->avatar_color,
            'role'          => $this->role,
        ];
    }
}

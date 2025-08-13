<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'slug'  => $this->slug,

            'avatar' => $this->politicalProfile?->logo_path
                ? asset('storage/' . $this->politicalProfile->logo_path)
                : null,

            'political_profile' => $this->whenLoaded('politicalProfile', function () {
                return new PoliticalProfileResource($this->politicalProfile);
            }),
        ];
    }
}

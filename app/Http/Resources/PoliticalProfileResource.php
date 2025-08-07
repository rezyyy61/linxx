<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PoliticalProfileResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'user' => [
                'id'       => $this->user->id,
                'name'     => $this->user->name,
                'username' => $this->user->username,
                'email'    => $this->user->email,
                'slug'     => $this->user->slug,
            ],
            'group_name'   => $this->group_name,
            'tagline'      => $this->tagline,
            'entity_type'  => $this->entity_type,
            'location'     => $this->location,
            'founded_year' => $this->founded_year,

            'logo_url' => $this->logo_path
                ? (preg_match('/^https?:\/\//', $this->logo_path)
                    ? $this->logo_path
                    : asset('storage/' . $this->logo_path))
                : ($this->user->avatar ?? null),

            'about'        => $this->about,
            'goals'        => $this->goals,
            'activities'   => $this->activities,
            'structure'    => $this->structure,
            'avatar_color' => $this->avatar_color,

            'links'        => $this->links->map(fn ($link) => [
                'id'    => $link->id,
                'type'  => $link->type,
                'title' => $link->title,
                'url'   => $link->url,
            ]),

            'ideologies'   => $this->ideologies->map(fn ($i) => [
                'id'    => $i->id,
                'value' => $i->value,
                'type'  => $i->type,
            ]),

            'keywords'     => $this->ideologies
                ->where('type', 'custom')
                ->pluck('value')
                ->values(),

            'files'        => $this->files->map(fn ($f) => [
                'id'        => $f->id,
                'section'   => $f->section,
                'name'      => $f->file_name,
                'url'       => Storage::disk('public')->url($f->file_path),
                'mime_type' => $f->mime_type,
                'size'      => $f->file_size,
            ]),
        ];
    }
}

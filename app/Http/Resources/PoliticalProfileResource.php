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
            'group_name'   => $this->group_name,
            'tagline'      => $this->tagline,
            'entity_type'  => $this->entity_type,
            'location'     => $this->location,
            'founded_year' => $this->founded_year,
            'logo_url'     => $this->logo_path ? asset('storage/' . $this->logo_path) : null,
            'about'        => $this->about,
            'goals'        => $this->goals,
            'activities'   => $this->activities,
            'structure'    => $this->structure,

            'links'        => $this->links,
            'ideologies'   => $this->ideologies,
            'files'        => $this->files->map(fn ($f) => [
                'id'        => $f->id,
                'section'   => $f->section,      // about | goals | activities | structure
                'name'      => $f->file_name,
                'url'       => Storage::disk('public')->url($f->file_path),
                'mime_type' => $f->mime_type,
            ]),
        ];
    }
}

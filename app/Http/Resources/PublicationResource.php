<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PublicationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'issue'        => $this->issue,
            'description'  => $this->description,
            'published_at' => optional($this->published_at)->format('Y-m-d'),
            'file_url'     => $this->file_path
                ? Storage::disk('public')->url($this->file_path)
                : null,
        ];
    }
}

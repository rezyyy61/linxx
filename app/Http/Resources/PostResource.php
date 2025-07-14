<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'created_at' => $this->created_at->toIso8601String(),
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'media' => $this->media
                ->where('status', 'done')
                ->map(function ($media) {
                    return [
                        'type' => $media->type,
                        'url'  => asset('storage/' . $media->path),
                        'meta' => $media->meta,
                        'original_name' => $this->meta['original_name'] ?? basename($this->path),
                        'download_url' => route('media.download', $media->id),
                    ];
                })->values(),

        ];
    }
}

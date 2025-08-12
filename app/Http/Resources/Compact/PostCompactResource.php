<?php

namespace App\Http\Resources\Compact;

use Illuminate\Http\Resources\Json\JsonResource;

class PostCompactResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'slug'       => (string)($this->slug ?? $this->id),
            'text'       => $this->text,
            'created_at' => optional($this->created_at)->toIso8601String(),

            'user'       => $this->whenLoaded('user', function () {
                return [
                    'id'           => $this->user->id,
                    'name'         => $this->user->name,
                    'slug'         => $this->user->slug,
                    'avatar_color' => optional($this->user->politicalProfile)->avatar_color,
                    'logo_url'     => optional($this->user->politicalProfile)?->logo_url,
                ];
            }),

            'media'      => $this->whenLoaded('media', function () {
                return $this->media
                    ->where('status', 'done')
                    ->map(function ($media) {
                        return [
                            'type'         => $media->type,
                            'url'          => asset('storage/' . $media->path),
                            'meta'         => $media->meta,
                            'download_url' => route('media.download', $media->id),
                        ];
                    })->values();
            }),
        ];
    }
}

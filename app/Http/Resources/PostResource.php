<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $user = Auth::guard('sanctum')->user();

        return [
            'id' => $this->id,
            'text' => $this->text,
            'created_at' => $this->created_at->toIso8601String(),
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'slug' => $this->user->slug,
                'avatar_color' => optional($this->user->politicalProfile)->avatar_color,
                'logo_url' => optional($this->user->politicalProfile)?->logo_url,
            ],
            'media' => $this->media
                ->where('status', 'done')
                ->map(function ($media) {
                    return [
                        'type' => $media->type,
                        'url'  => asset('storage/' . $media->path),
                        'meta' => $media->meta,
                        'download_url' => route('media.download', $media->id),
                    ];
                })->values(),
            'likes_count' => $this->likes->count(),
            'is_liked' => $user ? $this->isLikedBy($user) : false,
            'likes_preview' => $this->likes->take(3)->map(function ($like) {
                return [
                    'id' => $like->user->id,
                    'name' => $like->user->name,
                ];
            }),
            'comments_count' => $this->comments()->count(),

        ];
    }
}

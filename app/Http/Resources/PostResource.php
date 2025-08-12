<?php

namespace App\Http\Resources;

use App\Http\Resources\Compact\BookCompactResource;
use App\Http\Resources\Compact\EventCompactResource;
use App\Http\Resources\Compact\PostCompactResource;
use App\Models\Book\Book;
use App\Models\Event\Event;
use App\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        $user = Auth::guard('sanctum')->user();

        return [
            'id'         => $this->id,
            'slug'       => (string)($this->slug ?? $this->id),
            'text'       => $this->text,
            'created_at' => optional($this->created_at)->toIso8601String(),

            'user' => [
                'id'           => $this->user->id,
                'name'         => $this->user->name,
                'slug'         => $this->user->slug,
                'avatar_color' => optional($this->user->politicalProfile)->avatar_color,
                'logo_url'     => optional($this->user->politicalProfile)?->logo_url,
            ],

            'media' => $this->media
                ->where('status', 'done')
                ->map(fn($m) => [
                    'type'         => $m->type,
                    'url'          => asset('storage/'.$m->path),
                    'meta'         => $m->meta,
                    'download_url' => route('media.download', $m->id),
                ])->values(),

            'likes_count'    => $this->likes->count(),
            'is_liked'       => $user ? $this->isLikedBy($user) : false,
            'likes_preview'  => $this->likes->take(3)->map(fn($like) => [
                'id' => $like->user->id, 'name' => $like->user->name,
            ]),
            'comments_count' => $this->comments()->count(),

            'share_id' => $this->share_id,

            'share' => $this->when(
                $this->relationLoaded('share') && $this->share,
                function () {
                    $shareable = $this->share->relationLoaded('shareable') ? $this->share->shareable : null;

                    return [
                        'id'   => $this->share->id,
                        'slug' => $this->share->slug,
                        'type' => $this->share->shareable_type,
                        'shareable' => match (true) {
                            $shareable instanceof Post => new PostCompactResource($shareable),
                            $shareable instanceof Event => new EventCompactResource($shareable),
                            $shareable instanceof Book => new BookCompactResource($shareable),
                            default => null,
                        },
                    ];
                }
            ),
        ];
    }
}

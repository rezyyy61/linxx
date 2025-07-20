<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $request->user();

        return [
            'id' => $this->id,
            'content' => $this->content,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'like_count' => $this->like_count,
            'liked_by_user' => $user
                ? $this->likes()->where('user_id', $user->id)->exists()
                : false,
            'likes_preview' => $this->likes()
                ->with('user:id,name')
                ->latest()
                ->take(3)
                ->get()
                ->pluck('user')
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                    ];
                }),
            'replies_count' => $this->children->count(),
            'children' => CommentResource::collection($this->whenLoaded('childrenRecursive')),
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}

<?php

namespace App\Http\Resources\Compact;

use Illuminate\Http\Resources\Json\JsonResource;

class BookCompactResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'author'       => $this->author,
            'description'  => $this->description,
            'is_free'      => (bool) $this->is_free,
            'view_count'   => $this->view_count,
            'download_count' => $this->download_count,
            'rating' => round($this->reviews_avg_rating ?? 0, 1),
            'reviews_count' => $this->reviews_count ?? 0,

            'cover_image_url' => $this->cover_image
                ? asset('storage/' . $this->cover_image)
                : null,

            'category' => $this->whenLoaded('category', function () {
                return [
                    'id'   => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
            'user'        => $this->whenLoaded('creator', function () {
                return [
                    'id'           => $this->creator->id,
                    'name'         => $this->creator->name,
                    'slug'         => $this->creator->slug,
                    'avatar_color' => optional($this->creator->politicalProfile)->avatar_color,
                    'logo_url'     => optional($this->creator->politicalProfile)?->logo_url,
                ];
            }),
        ];
    }
}

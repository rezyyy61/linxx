<?php

namespace App\Http\Resources\Book;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'rating' => $this->rating,
            'text' => $this->review,
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user->name ?? 'Anonymous',
            ],
            'created_at' => $this->created_at->toDateTimeString()
        ];
    }

}

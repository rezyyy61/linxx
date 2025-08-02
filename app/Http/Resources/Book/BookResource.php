<?php

namespace App\Http\Resources\Book;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray($request): array
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

            'cover_image_url' => $this->cover_image
                ? asset('storage/' . $this->cover_image)
                : null,

            'file_download_url' => $this->file_path
                ? route('books.download', $this->id)
                : null,

            'category' => $this->whenLoaded('category', function () {
                return [
                    'id'   => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),

            'uploader' => $this->whenLoaded('uploader', function () {
                return [
                    'id'   => $this->uploader->id,
                    'name' => $this->uploader->name,
                ];
            }),

            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}


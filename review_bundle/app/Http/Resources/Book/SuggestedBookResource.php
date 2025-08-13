<?php

namespace App\Http\Resources\Book;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class SuggestedBookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'author'       => $this->author,
            'translator'   => $this->translator,
            'description'  => $this->description,
            'link'         => $this->link,
            'cover_url' => $this->cover_path
                ? URL::to(Storage::url($this->cover_path))
                : null,

            'file_url'  => $this->file_path
                ? URL::to(Storage::url($this->file_path))
                : null,
            'status'       => $this->status,
            'submitter'   => $this->submitter ? [
                'id'     => $this->submitter->id,
                'name'   => $this->submitter->name,
                'email'  => $this->submitter->email,
            ] : null,
            'created_at'   => $this->created_at,
        ];
    }
}

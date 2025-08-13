<?php

namespace App\Services;

use App\Models\Post;

class VideoPostService
{
    public function getPaginatedVideos($perPage = 12)
    {
        return Post::whereHas('media', function ($query) {
            $query->where('type', 'video')
                ->where('status', 'done');
        })
            ->with(['media' => function ($query) {
                $query->where('type', 'video')->where('status', 'done');
            }, 'user'])
            ->latest()
            ->paginate($perPage);
    }

    public function getVideoById($id)
    {
        return Post::where('id', $id)
            ->whereHas('media', fn($q) => $q->where('type', 'video')->where('status', 'done'))
            ->with([
                'media' => fn($q) => $q->where('type', 'video')->where('status', 'done'),
                'user',
                'likes.user',
            ])
            ->firstOrFail();
    }


}

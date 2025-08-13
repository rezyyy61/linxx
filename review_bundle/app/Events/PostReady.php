<?php

namespace App\Events;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class PostReady implements ShouldBroadcast
{
    use SerializesModels;

    public array $post;

    public function __construct(Post $post)
    {
        $this->post = PostResource::make($post->load(['user','media']))->resolve();
    }

    public function broadcastOn(): Channel
    {
        return new Channel('public-feed');
    }

    public function broadcastAs(): string
    {
        return 'PostReady';
    }
}


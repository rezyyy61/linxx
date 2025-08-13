<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostQueued implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $post;

    public function __construct(Post $post)
    {
        $this->post = [
            'id'     => $post->id,
            'user'   => $post->user->only(['id', 'name', 'avatar']),
            'text'   => $post->text,
            'status' => $post->status,
        ];
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('user.' . $this->post['user']['id']);
    }

    public function broadcastAs(): string
    {
        return 'PostQueued';
    }
}

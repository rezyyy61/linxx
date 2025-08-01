<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class PostLiked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $postId;
    public $likesCount;
    public $liked;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct($postId, $likesCount, $liked, $userId)
    {
        $this->postId = $postId;
        $this->likesCount = $likesCount;
        $this->liked = $liked;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        $channelName = "post.{$this->postId}";
        return new Channel($channelName);
    }

    /**
     * Data to broadcast with the event.
     */
    public function broadcastWith()
    {
        $payload = [
            'postId'     => $this->postId,
            'likesCount' => $this->likesCount,
            'liked'      => $this->liked,
            'userId'     => $this->userId,
        ];

        return $payload;
    }
}

<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class CommentLiked implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public Comment $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('post.' . $this->comment->post_id);
    }

    public function broadcastWith(): array
    {
        return [
            'comment_id' => $this->comment->id,
            'like_count' => $this->comment->like_count,
        ];
    }

    public function broadcastAs(): string
    {
        return 'comment.liked';
    }
}

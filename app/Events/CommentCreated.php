<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;


class CommentCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public Comment $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment->load('user', 'childrenRecursive');
    }

    public function broadcastOn(): Channel
    {
        return new Channel('post.' . $this->comment->post_id);
    }

    public function broadcastWith(): array
    {
        $postId = $this->comment->post_id;
        $total = \App\Models\Comment::where('post_id', $postId)
            ->where('status', 'visible')
            ->count();

        return [
            'comment' => (new \App\Http\Resources\CommentResource($this->comment))->resolve(),
            'total_comments' => $total
        ];
    }


    public function broadcastAs(): string
    {
        return 'comment.created';
    }
}


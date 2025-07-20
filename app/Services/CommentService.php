<?php

namespace App\Services;

use App\Events\CommentCreated;
use App\Events\CommentLiked;
use App\Models\Comment;
use App\Models\CommentLike;
use Carbon\Carbon;

class CommentService
{
    public function store(array $data, int $userId): Comment
    {
        $data['user_id'] = $userId;
        $commentCount = Comment::where('user_id', $userId)
            ->where('created_at', '>=', Carbon::now()->subMinute())
            ->count();

        if ($commentCount >= 5) {
            abort(429, 'You have exceeded the comment limit (5 per minute).');
        }

        $comment = Comment::create($data);

        broadcast(new CommentCreated($comment))->toOthers();

        return $comment;
    }

    public function toggleLike(Comment $comment, int $userId): void
    {
        $like = CommentLike::where('comment_id', $comment->id)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            $like->delete();
            $comment->decrement('like_count');
        } else {
            CommentLike::create([
                'comment_id' => $comment->id,
                'user_id' => $userId,
            ]);
            $comment->increment('like_count');
        }
        broadcast(new CommentLiked($comment->fresh()))->toOthers();
    }

    public function getCommentsForPost(int $postId)
    {
        return Comment::with([
            'childrenRecursive',
            'user',
            'likes.user'
        ])
            ->where('post_id', $postId)
            ->whereNull('parent_id')
            ->where('status', 'visible')
            ->orderBy('created_at')
            ->get();

    }
}

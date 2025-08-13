<?php

namespace App\Services;

use App\Events\CommentCreated;
use App\Events\CommentLiked;
use App\Models\Comment;
use App\Models\CommentLike;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

        if (!empty($data['parent_id'])) {
            $parent = Comment::findOrFail($data['parent_id']);
            $data['parent_id'] = $parent->parent_id ?? $parent->id;
        }

        $comment = Comment::create($data);

        $comment->loadMissing('user.politicalProfile');

        broadcast(new CommentCreated($comment));

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
        return Comment::with(['user', 'likes.user', 'post',])
            ->where('post_id', $postId)
            ->where('status', 'visible')
            ->orderBy('created_at')
            ->get();
    }

}

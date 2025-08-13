<?php

namespace App\Http\Controllers\Api;

use App\Events\PostLiked;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Facades\Notifier;

class LikeController extends Controller
{
    public function toggle(Post $post, Request $request)
    {
        $user = $request->user();

        $like = $post->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();

            $likesCount = $post->likes()->count();

            broadcast(new PostLiked(
                $post->id,
                $likesCount,
                false,
                $user->id
            ));

            return response()->json([
                'liked' => false,
                'likes_count' => $likesCount
            ]);
        }

        try {
            $post->likes()->create(['user_id' => $user->id]);
        } catch (\Throwable $e) {
            Log::error('🔥 Like creation failed', [
                'postId' => $post->id,
                'userId' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'liked' => false,
                'likes_count' => $post->likes()->count(),
                'error' => 'Failed to like'
            ], 500);
        }

        $likesCount = $post->likes()->count();

        broadcast(new PostLiked(
            $post->id,
            $likesCount,
            true,
            $user->id
        ));

        if ((int) $post->user_id !== (int) $user->id) {
            Notifier::likePost(
                recipientId: (int) $post->user_id,
                postId: (int) $post->id,
                actor: [
                    'id' => (int) $user->id,
                    'name' => $user->name ?? $user->slug ?? 'User',
                    'avatar' => $user->avatar ?? null,
                ],
                url: url('/posts/' . $post->id)
            );
        }

        return response()->json([
            'liked' => true,
            'likes_count' => $likesCount
        ]);
    }

    public function preview(Post $post)
    {
        $users = $post->likes()
            ->with('user:id,name')
            ->latest()
            ->take(5)
            ->get()
            ->pluck('user');

        return response()->json([
            'users' => $users
        ]);
    }
}

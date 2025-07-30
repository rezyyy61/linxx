<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Post $post, Request $request)
    {
        $user = $request->user();

        $like = $post->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            return response()->json([
                'liked' => false,
                'likes_count' => $post->likes()->count()
            ]);
        }

        $post->likes()->create(['user_id' => $user->id]);

        return response()->json([
            'liked' => true,
            'likes_count' => $post->likes()->count()
        ]);
    }
}

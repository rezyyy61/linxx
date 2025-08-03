<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function store(StorePostRequest $request): \Illuminate\Http\JsonResponse
    {
        $media = [
            'images' => $request->file('images', []),
            'videos' => array_filter([$request->file('video')]),
            'audio' => array_filter([$request->file('audio')]),
            'files'  => $request->file('files', []),
        ];


        $post = $this->postService->createPost(
            $request->validated(),
            $media,
            $request->user()
        );

        return response()->json([
            'message'   => 'Post created; media processing queued',
            'post_id'   => $post->id,
            'mediaJobs' => $post->media->pluck('id'),
        ]);

    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $posts = Post::with([
            'user.politicalProfile',
            'media',
            'likes.user',
        ])
            ->latest()
            ->paginate(10);

        return PostResource::collection($posts);
    }

}

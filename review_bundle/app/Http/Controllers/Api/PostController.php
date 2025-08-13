<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
            'audio'  => array_filter([$request->file('audio')]),
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

    public function index()
    {
        $posts = Post::query()
            ->with([
                'user.politicalProfile',
                'media',
                'likes.user',
                'comments',
                'share',
                // morphWith برای هر نوع shareable
                'share.shareable' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        \App\Models\Post::class => ['user.politicalProfile', 'media'],
                        \App\Models\Event\Event::class => ['user.politicalProfile'],
                    ]);
                },
            ])
            ->latest()
            ->paginate(10);

        return PostResource::collection($posts);
    }

    public function show(int $id): PostResource
    {
        $post = Post::query()
            ->with([
                'user.politicalProfile',
                'media',
                'likes.user',
                'comments',
                'share',
                'share.shareable' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        \App\Models\Post::class => ['user.politicalProfile', 'media'],
                        \App\Models\Event\Event::class => ['user.politicalProfile'],
                    ]);
                },
            ])
            ->findOrFail($id);

        return new PostResource($post);
    }

}

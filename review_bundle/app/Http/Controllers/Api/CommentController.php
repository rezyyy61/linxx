<?php

namespace App\Http\Controllers\Api;

use App\Events\CommentDeleted;
use App\Events\CommentUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use AuthorizesRequests;

    protected CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function store(StoreCommentRequest $request)
    {
        $comment = $this->commentService->store(
            $request->validated(),
            auth()->id()
        );

        return (new CommentResource($comment))
            ->additional(['message' => 'Comment posted successfully.'])
            ->response()
            ->setStatusCode(201);
    }


    public function like(Comment $comment)
    {
        $this->commentService->toggleLike($comment, auth()->id());

        return response()->json([
            'message' => 'Like status updated.'
        ]);
    }

    public function index(Request $request)
    {
        $comments = $this->commentService->getCommentsForPost($request->post_id);

        return CommentResource::collection($comments);
    }

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment->update([
            'content' => $request->input('content'),
        ]);
        broadcast(new CommentUpdated($comment))->toOthers();

        return (new CommentResource($comment))
            ->additional(['message' => 'Comment updated.']);
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $commentId = $comment->id;
        $postId    = $comment->post_id;
        $comment->delete();

        broadcast(new CommentDeleted($commentId, $postId))->toOthers();

        return response()->json([
            'message' => 'Comment deleted.',
        ]);
    }

    public function report(Request $request, Comment $comment)
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:500']
        ]);

        $userId = auth()->id();

        $alreadyReported = $comment->reports()
            ->where('user_id', $userId)
            ->exists();

        if ($alreadyReported) {
            return response()->json([
                'message' => 'You have already reported this comment.'
            ], 422);
        }

        $comment->reports()->create([
            'user_id' => $userId,
            'reason' => $request->input('reason'),
        ]);

        return response()->json([
            'message' => 'Your report has been submitted.'
        ]);
    }

}

<?php

namespace App\Http\Controllers\Api\Share;

use App\Http\Controllers\Controller;
use App\Http\Requests\Share\ReshareRequest;
use App\Http\Resources\PostResource;
use App\Models\Book\Book;
use App\Models\Event\Event;
use App\Models\Post;
use App\Services\Share\Contracts\ShareResolver;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReshareController extends Controller
{
    public function __invoke(ReshareRequest $request, ShareResolver $resolver): JsonResponse
    {
        $slug = $request->string('share_slug')->toString();
        $result = $resolver->resolveBySlug($slug);

        if ($result->status->value !== 'ok') {
            throw ValidationException::withMessages([
                'share_slug' => 'This share is not available ('.$result->status->value.').',
            ]);
        }

        $share = $result->share;

        if (property_exists($share, 'allow_repost') && !$share->allow_repost) {
            throw ValidationException::withMessages([
                'share_slug' => 'Repost is not allowed for this link.',
            ]);
        }

        $shareable = $share->shareable;

        if (!($shareable instanceof Post || $shareable instanceof Event || $shareable instanceof Book)) {
            throw ValidationException::withMessages([
                'share_slug' => 'Only posts, events or books can be reshared.',
            ]);
        }

        // جلوگیری از بازنشر تو‌در‌تو: اگر share روی پستی است که خودش بازنشر است، به share اصلی اشاره کن
        $finalShareId = $share->id;
        if ($shareable instanceof Post && $shareable->share_id) {
            $finalShareId = $shareable->share_id;
        }

        $user = $request->user();
        $text = $request->input('text');

        $post = DB::transaction(function () use ($user, $finalShareId, $text) {
            return Post::create([
                'user_id'    => $user->id,
                'text'       => $text,
                'visibility' => 'public',
                'status'     => 'ready',
                'share_id'   => $finalShareId,
            ]);
        });

        $post->load([
            'user.politicalProfile',
            'media',
            'likes.user',
            'comments',
            'share',
            'share.shareable' => function (MorphTo $morphTo) {
                $morphTo->morphWith([
                    Post::class  => ['user.politicalProfile', 'media'],
                    Event::class => ['user.politicalProfile'],
                    Book::class => ['user.politicalProfile'],
                ]);
            },
        ]);

        return response()->json(new PostResource($post), 201);
    }
}

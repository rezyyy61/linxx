<?php

namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notifications\UserNotificationResource;
use App\Models\Notifications\UserNotification;
use App\Models\Notifications\UserNotificationCounter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $userId = (int) $request->user()->id;
        $unread = (int) $request->boolean('unread', false);
        $q = UserNotification::query()->where('user_id', $userId)->orderByDesc('created_at');
        if ($unread) $q->whereNull('read_at');
        $items = $q->paginate((int) $request->integer('per_page', 20));
        $counter = UserNotificationCounter::query()->find($userId);
        return UserNotificationResource::collection($items)->additional([
            'meta' => [
                'unseen' => (int) ($counter->unseen_count ?? 0),
                'unread' => (int) ($counter->unread_count ?? 0),
            ]
        ]);
    }

    public function counter(Request $request)
    {
        $userId = (int) $request->user()->id;
        $counter = UserNotificationCounter::query()->find($userId);
        return response()->json([
            'unseen' => (int) ($counter->unseen_count ?? 0),
            'unread' => (int) ($counter->unread_count ?? 0),
        ]);
    }

    public function seen(Request $request)
    {
        $userId = (int) $request->user()->id;
        DB::transaction(function () use ($userId) {
            UserNotification::query()->where('user_id', $userId)->whereNull('seen_at')->update(['seen_at' => now()]);
            $counter = UserNotificationCounter::query()->firstOrCreate(['user_id' => $userId]);
            $counter->unseen_count = 0;
            $counter->save();
        });
        return response()->noContent();
    }

    public function read(Request $request, string $id)
    {
        $userId = (int) $request->user()->id;
        DB::transaction(function () use ($userId, $id) {
            $n = UserNotification::query()->where('user_id', $userId)->where('id', $id)->firstOrFail();
            if (is_null($n->read_at)) {
                $n->read_at = now();
                if (is_null($n->seen_at)) $n->seen_at = $n->read_at;
                $n->save();
                $counter = UserNotificationCounter::query()->firstOrCreate(['user_id' => $userId]);
                $counter->unread_count = max(0, (int) $counter->unread_count - 1);
                $counter->unseen_count = max(0, (int) $counter->unseen_count - 1);
                $counter->save();
            }
        });
        return response()->noContent();
    }

    public function readAll(Request $request)
    {
        $userId = (int) $request->user()->id;
        DB::transaction(function () use ($userId) {
            UserNotification::query()->where('user_id', $userId)->whereNull('read_at')->update(['read_at' => now(), 'seen_at' => now()]);
            $counter = UserNotificationCounter::query()->firstOrCreate(['user_id' => $userId]);
            $counter->unread_count = 0;
            $counter->unseen_count = 0;
            $counter->save();
        });
        return response()->noContent();
    }
}

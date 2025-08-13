<?php

namespace App\Services\Notifications;

use App\Events\Notifications\UserNotificationBroadcasted;
use App\Models\Notifications\NotificationEventKey;
use App\Models\Notifications\UserNotification;
use App\Models\Notifications\UserNotificationCounter;
use App\Models\Notifications\UserNotificationPreference;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function process(array $data): ?UserNotification
    {
        $recipientId = (int) ($data['recipient_id'] ?? 0);
        if ($recipientId <= 0) return null;

        $type = (string) ($data['type'] ?? '');
        $subjectType = (string) ($data['subject_type'] ?? '');
        $subjectId = (int) ($data['subject_id'] ?? 0);
        if ($type === '' || $subjectType === '' || $subjectId <= 0) return null;

        $eventUid = (string) ($data['event_uid'] ?? '');
        if ($eventUid !== '') {
            $exists = NotificationEventKey::query()->where('event_uid', $eventUid)->where('user_id', $recipientId)->exists();
            if ($exists) return null;
        }

        if (!$this->inAppAllowed($recipientId)) return null;

        $groupKey = (string) ($data['group_key'] ?? '');
        $actor = Arr::only((array) ($data['actor'] ?? []), ['id', 'name', 'avatar']);

        $notification = DB::transaction(function () use ($recipientId, $type, $subjectType, $subjectId, $groupKey, $data, $actor, $eventUid) {
            $aggregateWindow = $this->aggregateWindowSeconds($type);
            $nowWindow = now()->subSeconds($aggregateWindow);

            $existing = null;
            if ($groupKey !== '' && $aggregateWindow > 0) {
                $existing = UserNotification::query()
                    ->where('user_id', $recipientId)
                    ->where('type', $type)
                    ->where('group_key', $groupKey)
                    ->where('created_at', '>=', $nowWindow)
                    ->orderByDesc('created_at')
                    ->first();
            }

            if ($existing) {
                $actors = is_array($existing->actors) ? $existing->actors : [];
                if (!empty($actor)) {
                    $actors = $this->mergeActors($actors, $actor, 3);
                }
                $existing->actors = $actors;
                $existing->count = (int) $existing->count + 1;
                $existing->title = $data['title'] ?? $existing->title;
                $existing->body = $data['body'] ?? $existing->body;
                $existing->action_url = $data['action_url'] ?? $existing->action_url;
                $existing->save();
                $notification = $existing;
                if ($eventUid !== '') {
                    NotificationEventKey::query()->firstOrCreate([
                        'event_uid' => $eventUid,
                        'user_id' => $recipientId,
                    ], ['type' => $type]);
                }
                return $notification;
            }

            $notification = new UserNotification();
            $notification->user_id = $recipientId;
            $notification->type = $type;
            $notification->subject_type = $subjectType;
            $notification->subject_id = $subjectId;
            $notification->group_key = $groupKey ?: null;
            $notification->title = (string) ($data['title'] ?? null);
            $notification->body = (string) ($data['body'] ?? null);
            $notification->action_url = (string) ($data['action_url'] ?? null);
            $notification->actors = !empty($actor) ? [$actor] : [];
            $notification->count = 1;
            $notification->level = (string) ($data['level'] ?? 'info');
            $notification->save();

            $this->bumpCountersOnCreate($recipientId);

            if ($eventUid !== '') {
                NotificationEventKey::query()->firstOrCreate([
                    'event_uid' => $eventUid,
                    'user_id' => $recipientId,
                ], ['type' => $type]);
            }

            return $notification;
        });

        if ($notification) {
            event(new UserNotificationBroadcasted($notification));
        }

        return $notification;
    }

    protected function inAppAllowed(int $userId): bool
    {
        $pref = UserNotificationPreference::query()->find($userId);
        if ($pref && !$pref->inapp_enabled) return false;
        if ($pref && $pref->mute_until && now()->lessThan($pref->mute_until)) return false;
        if ($pref && $pref->quiet_hours_from && $pref->quiet_hours_to) {
            $from = $pref->quiet_hours_from;
            $to = $pref->quiet_hours_to;
            $now = now($pref->timezone ?: config('app.timezone'));
            $cur = $now->format('H:i:s');
            if ($from <= $to) {
                if ($cur >= $from && $cur <= $to) return false;
            } else {
                if ($cur >= $from || $cur <= $to) return false;
            }
        }
        return true;
    }

    protected function bumpCountersOnCreate(int $userId): void
    {
        $counter = UserNotificationCounter::query()->firstOrCreate(['user_id' => $userId]);
        $counter->unseen_count = (int) $counter->unseen_count + 1;
        $counter->unread_count = (int) $counter->unread_count + 1;
        $counter->save();
    }

    protected function mergeActors(array $actors, array $newActor, int $limit): array
    {
        $existingIds = collect($actors)->pluck('id')->all();
        if (isset($newActor['id']) && !in_array($newActor['id'], $existingIds)) {
            array_unshift($actors, $newActor);
        }
        $actors = collect($actors)->filter(function ($a) {
            return isset($a['id']);
        })->unique('id')->take($limit)->values()->all();
        return $actors;
    }

    protected function aggregateWindowSeconds(string $type): int
    {
        if ($type === 'like.post') return 300;
        if ($type === 'comment.post') return 1200;
        return 0;
    }
}

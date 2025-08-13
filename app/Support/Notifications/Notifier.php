<?php

namespace App\Support\Notifications;

use App\Enums\NotificationType;
use App\Jobs\Notifications\ProcessNotification;
use Illuminate\Support\Str;

class Notifier
{
    public function notify(array $payload): void
    {
        ProcessNotification::dispatch($payload)->onQueue('notifications')->afterCommit();
    }

    public function likePost(int $recipientId, int $postId, array $actor, ?string $url = null, ?string $eventUid = null, ?string $title = null, ?string $body = null): void
    {
        $this->notify([
            'recipient_id' => $recipientId,
            'type' => NotificationType::LikePost->value,
            'subject_type' => \App\Models\Post::class,
            'subject_id' => $postId,
            'group_key' => 'like:post:' . $postId,
            'actor' => $actor,
            'title' => $title ?? 'Post liked',
            'body' => $body ?? (($actor['name'] ?? 'Someone') . ' liked your post'),
            'action_url' => $url ?? url('/posts/' . $postId),
            'event_uid' => $eventUid ?? (string) Str::uuid(),
        ]);
    }

    public function commentPost(int $recipientId, int $postId, array $actor, int $commentId, ?string $url = null, ?string $eventUid = null, ?string $title = null, ?string $body = null): void
    {
        $this->notify([
            'recipient_id' => $recipientId,
            'type' => NotificationType::CommentPost->value,
            'subject_type' => \App\Models\Post::class,
            'subject_id' => $postId,
            'group_key' => 'comment:post:' . $postId,
            'actor' => $actor,
            'title' => $title ?? 'New comment',
            'body' => $body ?? (($actor['name'] ?? 'Someone') . ' commented on your post'),
            'action_url' => $url ?? url('/posts/' . $postId . '#comment-' . $commentId),
            'event_uid' => $eventUid ?? (string) Str::uuid(),
        ]);
    }

    public function replyComment(int $recipientId, int $commentId, array $actor, ?string $url = null, ?string $eventUid = null, ?string $title = null, ?string $body = null): void
    {
        $this->notify([
            'recipient_id' => $recipientId,
            'type' => NotificationType::ReplyComment->value,
            'subject_type' => \App\Models\Comment::class,
            'subject_id' => $commentId,
            'group_key' => null,
            'actor' => $actor,
            'title' => $title ?? 'New reply',
            'body' => $body ?? (($actor['name'] ?? 'Someone') . ' replied to your comment'),
            'action_url' => $url ?? url('/comments/' . $commentId),
            'event_uid' => $eventUid ?? (string) Str::uuid(),
        ]);
    }

    public function mentionUser(int $recipientId, string $subjectType, int $subjectId, array $actor, ?string $url = null, ?string $eventUid = null, ?string $title = null, ?string $body = null): void
    {
        $this->notify([
            'recipient_id' => $recipientId,
            'type' => NotificationType::MentionUser->value,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'group_key' => null,
            'actor' => $actor,
            'title' => $title ?? 'You were mentioned',
            'body' => $body ?? (($actor['name'] ?? 'Someone') . ' mentioned you'),
            'action_url' => $url ?? url('/'),
            'event_uid' => $eventUid ?? (string) Str::uuid(),
        ]);
    }
}

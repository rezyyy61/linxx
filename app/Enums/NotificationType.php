<?php

namespace App\Enums;

enum NotificationType: string
{
    case LikePost = 'like.post';
    case CommentPost = 'comment.post';
    case ReplyComment = 'reply.comment';
    case MentionUser = 'mention.user';
}

<?php

namespace App\Models\Notifications;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserNotification extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'user_notifications';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'type',
        'subject_type',
        'subject_id',
        'group_key',
        'title',
        'body',
        'action_url',
        'actors',
        'count',
        'level',
        'seen_at',
        'read_at',
    ];

    protected $casts = [
        'actors' => 'array',
        'count' => 'integer',
        'seen_at' => 'datetime',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForUser($q, int $userId)
    {
        return $q->where('user_id', $userId);
    }

    public function scopeUnread($q)
    {
        return $q->whereNull('read_at');
    }

    public function scopeUnseen($q)
    {
        return $q->whereNull('seen_at');
    }

    public function scopeRecent($q)
    {
        return $q->orderByDesc('created_at');
    }

    public function markSeen(): bool
    {
        if ($this->seen_at) return true;
        $this->seen_at = now();
        return $this->save();
    }

    public function markRead(): bool
    {
        if ($this->read_at) return true;
        $now = now();
        if (!$this->seen_at) $this->seen_at = $now;
        $this->read_at = $now;
        return $this->save();
    }
}

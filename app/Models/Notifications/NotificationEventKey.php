<?php

namespace App\Models\Notifications;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationEventKey extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'notification_event_keys';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'event_uid',
        'user_id',
        'type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

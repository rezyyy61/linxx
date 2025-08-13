<?php

namespace App\Models\Notifications;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotificationPreference extends Model
{
    use HasFactory;

    protected $table = 'user_notification_preferences';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'inapp_enabled',
        'toast_enabled',
        'types',
        'mute_until',
        'quiet_hours_from',
        'quiet_hours_to',
        'timezone',
    ];

    protected $casts = [
        'inapp_enabled' => 'boolean',
        'toast_enabled' => 'boolean',
        'types' => 'array',
        'mute_until' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

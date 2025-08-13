<?php

namespace App\Models\Event;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRsvp extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'checked_in_at',
        'notes',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models\Event;


use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'creator_type',
        'creator_id',
        'created_by_user_id',
        'title',
        'description',
        'type',
        'type_custom',
        'mode',
        'visibility',
        'status',
        'allow_comments',
        'timezone',
        'starts_at',
        'ends_at',
        'rsvp_deadline',
        'capacity',
        'requires_approval',
        'venue_name',
        'address',
        'city',
        'country',
        'lat',
        'lng',
        'going_count',
        'interested_count',
        'cover_path',
        'attachment_path',
        'platform',
        'meeting_link',
        'access_code',
    ];

    protected $casts = [
        'allow_comments'    => 'boolean',
        'requires_approval' => 'boolean',
        'starts_at'         => 'datetime',
        'ends_at'           => 'datetime',
        'rsvp_deadline'     => 'datetime',
        'lat'               => 'decimal:7',
        'lng'               => 'decimal:7',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function creator()
    {
        return $this->morphTo();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function rsvps()
    {
        return $this->hasMany(EventRsvp::class);
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_rsvps')
            ->withPivot('status', 'checked_in_at')
            ->withTimestamps();
    }

    public function invites()
    {
        return $this->hasMany(EventInvite::class);
    }

    public function organizers()
    {
        return $this->morphToMany(User::class, 'organizer', 'event_organizers');
    }

    public function scopePublicVisible($q)
    {
        return $q->where('visibility', 'public')->where('status', 'published');
    }

    public function scopeUpcoming($q, $enable = true)
    {
        if (!$enable) return $q;
        return $q->where('starts_at', '>=', now());
    }

    public function scopeSearch($q, ?string $term)
    {
        if (!$term) return $q;
        $like = '%'.$term.'%';
        return $q->where(function ($w) use ($like) {
            $w->where('title', 'like', $like)
                ->orWhere('description', 'like', $like)
                ->orWhere('city', 'like', $like)
                ->orWhere('venue_name', 'like', $like);
        });
    }

    public function scopeTypeIs($q, ?string $type)
    {
        if (!$type) return $q;
        return $q->where(function ($w) use ($type) {
            $w->where('type', $type)
                ->orWhere(function ($w2) use ($type) {
                    $w2->where('type', 'custom')->where('type_custom', $type);
                });
        });
    }

    public function scopeModeIs($q, ?string $mode)
    {
        if (!$mode) return $q;
        return $q->where('mode', $mode);
    }

    public function scopeCityIs($q, ?string $city)
    {
        if (!$city) return $q;
        return $q->where('city', $city);
    }

    public function scopeCreatorIs($q, $id)
    {
        if (!$id) return $q;
        return $q->where('creator_id', $id);
    }

    public function scopeStartsBetween($q, ?string $from, ?string $to)
    {
        if ($from) $q->whereDate('starts_at', '>=', $from);
        if ($to) $q->whereDate('starts_at', '<=', $to);
        return $q;
    }

    public function getCoverUrlAttribute(): ?string
    {
        $path = $this->cover_path;
        if (!$path) {
            return null;
        }

        if (function_exists('str_starts_with') && str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path ? Storage::disk('public')->url($this->attachment_path) : null;
    }
}

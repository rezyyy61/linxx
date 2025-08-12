<?php

namespace App\Models;

use App\Models\Share\Share;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'text',
        'visibility',
        'status',
        'is_archived',
        'share_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function media(): HasMany
    {
        return $this->hasMany(PostMedia::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function publication()
    {
        return $this->hasOne(Publication::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function share(): BelongsTo
    {
        return $this->belongsTo(Share::class);
    }

    public function getIsReshareAttribute(): bool
    {
        return !is_null($this->share_id);
    }

    public function getOriginalPostAttribute(): ?Post
    {
        return ($this->share && $this->share->shareable instanceof Post)
            ? $this->share->shareable
            : null;
    }

    public function isLikedBy($user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function getFullUrlAttribute(): string
    {
        return $this->url ?? Storage::disk('public')->url($this->path);
    }

    public function getFullPosterAttribute(): ?string
    {
        return $this->poster ? Storage::disk('public')->url($this->poster) : null;
    }

}

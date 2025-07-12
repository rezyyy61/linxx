<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'type',
        'path',
        'order',
        'duration',
        'meta',
        'meta_tmp',
        'status',
        'error'
    ];

    protected $casts = [
        'duration' => 'integer',
        'order' => 'integer',
        'meta' => 'array',
        'meta_tmp' => 'array',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->type === 'image' ? asset('storage/' . $this->path) : null;
    }

    public function getFullUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}


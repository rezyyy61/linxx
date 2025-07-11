<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'issue',
        'description',
        'published_at',
        'file_path',
        'file_type',
    ];

    protected $casts = [
        'published_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function politicalProfile(): BelongsTo
    {
        return $this->belongsTo(PoliticalProfile::class);
    }

}

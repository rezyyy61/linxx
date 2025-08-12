<?php

namespace App\Models\Book;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'author',
        'description',
        'cover_image',
        'file_path',
        'is_free',
        'category_id',
        'uploaded_by',
        'view_count',
        'download_count',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_books')
            ->withPivot(['is_downloaded', 'is_read'])
            ->withTimestamps();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(BookReview::class);
    }
}

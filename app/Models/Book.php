<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function category()
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_books')
            ->withPivot(['is_downloaded', 'is_read'])
            ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(BookReview::class);
    }
}

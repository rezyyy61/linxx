<?php

namespace App\Models\Book;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SuggestedBook extends Model
{
    protected $fillable = [
        'title',
        'author',
        'translator',
        'description',
        'link',
        'cover_path',
        'file_path',
        'is_approved',
        'submitted_by',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'political_profile_id',
        'section',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(PoliticalProfile::class, 'political_profile_id');
    }
}

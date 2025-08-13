<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'political_profile_id',
        'type',
        'title',
        'url',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(PoliticalProfile::class, 'political_profile_id');
    }
}

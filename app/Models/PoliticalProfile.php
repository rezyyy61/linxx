<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoliticalProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_name',
        'tagline',
        'entity_type',
        'location',
        'founded_year',
        'logo_path',
        'about',
        'goals',
        'activities',
        'structure',
        'avatar_color'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(ProfileLink::class);
    }

    public function ideologies(): HasMany
    {
        return $this->hasMany(ProfileIdeology::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProfileFile::class);
    }

    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoliticalProfile extends Model
{
    use HasFactory;

    public const ORG_TYPES = ['party', 'collective', 'media'];

    protected $fillable = [
        'user_id',
        'tagline',
        'entity_type',
        'location',
        'founded_year',
        'logo_path',
        'about',
        'goals',
        'activities',
        'structure',
        'avatar_color',
        'pending_entity_type',
        'entity_type_approved',
    ];

    protected $appends = ['logo_url'];

    protected $casts = [
        'entity_type_approved' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo_path) {
            return preg_match('/^https?:\/\//', $this->logo_path)
                ? $this->logo_path
                : asset('storage/' . $this->logo_path);
        }
        return $this->user->avatar ?? null;
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

    public function scopeOrganizations($q)
    {
        return $q->whereIn('entity_type', self::ORG_TYPES);
    }

    public function scopeIndividuals($q)
    {
        return $q->where('entity_type', 'individual');
    }

    public function scopeOfTypes($q, array $types)
    {
        return $q->whereIn('entity_type', $types);
    }
}

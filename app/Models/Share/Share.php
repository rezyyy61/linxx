<?php

namespace App\Models\Share;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Share extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'shareable_type',
        'shareable_id',
        'scope',
        'slug',
        'password_hash',
        'expires_at',
        'revoked_at',
        'max_clicks',
        'clicks_count',
        'allow_repost',
        'extra'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'extra' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shareable()
    {
        return $this->morphTo();
    }

    public function clicks()
    {
        return $this->hasMany(ShareClick::class);
    }

    public function permissions()
    {
        return $this->hasMany(SharePermission::class);
    }

    public function meta()
    {
        return $this->hasMany(ShareMeta::class);
    }
}

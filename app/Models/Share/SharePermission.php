<?php

namespace App\Models\Share;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SharePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'share_id',
        'targetable_type',
        'targetable_id',
        'permission_type'
    ];

    public function share()
    {
        return $this->belongsTo(Share::class);
    }

    public function targetable()
    {
        return $this->morphTo();
    }
}

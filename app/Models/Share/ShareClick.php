<?php

namespace App\Models\Share;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShareClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'share_id',
        'clicked_at',
        'ip_address',
        'user_agent',
        'referrer_host',
        'referrer_path',
        'referrer_hash',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'country_code',
        'device_type',
        'fingerprint'
    ];

    protected $casts = [
        'clicked_at' => 'datetime'
    ];

    public function share()
    {
        return $this->belongsTo(Share::class);
    }
}

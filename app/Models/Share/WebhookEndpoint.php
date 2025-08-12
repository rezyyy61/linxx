<?php

namespace App\Models\Share;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebhookEndpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_client_id',
        'url',
        'url_hash',
        'events',
        'secret',
        'active',
        'last_status_code',
        'last_delivered_at',
        'failure_count'
    ];

    protected $casts = [
        'events' => 'array',
        'active' => 'boolean',
        'last_delivered_at' => 'datetime'
    ];

    public function apiClient()
    {
        return $this->belongsTo(ApiClient::class);
    }
}

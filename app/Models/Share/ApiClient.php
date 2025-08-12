<?php

namespace App\Models\Share;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApiClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'client_id',
        'client_secret_hash',
        'callback_url',
        'scopes',
        'active',
        'rate_limit_per_minute'
    ];

    protected $casts = [
        'scopes' => 'array',
        'active' => 'boolean'
    ];

    public function webhookEndpoints()
    {
        return $this->hasMany(WebhookEndpoint::class);
    }
}

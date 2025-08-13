<?php

namespace App\Models\Share;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShareMeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'share_id',
        'key',
        'value'
    ];

    public function share()
    {
        return $this->belongsTo(Share::class);
    }
}

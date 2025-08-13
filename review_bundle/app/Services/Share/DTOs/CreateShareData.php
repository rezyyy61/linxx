<?php

namespace App\Services\Share\DTOs;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CreateShareData
{
    public function __construct(
        public readonly Authenticatable $actor,
        public readonly Model $shareable,
        public readonly string $scope = 'public',
        public readonly ?Carbon $expiresAt = null,
        public readonly ?int $maxClicks = null,
        public readonly bool $allowRepost = true,
        public readonly ?string $password = null,
        public readonly array $extra = []
    ) {}
}

<?php

namespace App\Services\Share\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

interface PermissionGate
{
    public function canShare(Authenticatable $actor, Model $shareable): bool;
}

<?php

namespace App\Services\Share\Implementations;

use App\Models\User;
use App\Services\Share\Contracts\PermissionGate;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class DefaultPermissionGate implements PermissionGate
{
    public function canShare(Authenticatable $actor, Model $shareable): bool
    {
        if (method_exists($shareable, 'user') && $shareable->user instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
            return true;
        }
        if ($shareable->getAttribute('user_id') !== null && $actor instanceof User) {
            return true;
        }
        return true;
    }
}

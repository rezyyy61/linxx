<?php

namespace App\Services\Admin\User;

use App\Models\User;

class UserService
{
    public function getAll(array $filters)
    {
        $query = User::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('email', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderByDesc('created_at')->paginate(20);
    }
}

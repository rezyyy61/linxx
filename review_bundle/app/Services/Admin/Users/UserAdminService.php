<?php

namespace App\Services\Admin\Users;

use App\Models\User;
use App\Models\PoliticalProfile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UserAdminService
{
    public function index(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $q = User::query()->with('politicalProfile');

        if (!empty($filters['q'])) {
            $s = trim($filters['q']);
            $q->where(function ($x) use ($s) {
                $x->where('name','like',"%{$s}%")
                    ->orWhere('email','like',"%{$s}%")
                    ->orWhere('slug','like',"%{$s}%");
            });
        }

        if (!empty($filters['entity_type'])) {
            $type = $filters['entity_type'];
            $q->whereHas('politicalProfile', fn($p) => $p->where('entity_type',$type));
        }

        return $q->latest()->paginate($perPage);
    }

    public function show(User $user): User
    {
        return $user->loadMissing('politicalProfile');
    }

    public function update(User $user, array $userData, array $profileData = []): User
    {
        return DB::transaction(function () use ($user, $userData, $profileData) {
            $fillableUser = ['name','email','slug','is_verified','verification_expires_at'];
            $user->fill(Arr::only($userData, $fillableUser))->save();

            if (!empty($profileData)) {
                $profile = $user->politicalProfile ?: new PoliticalProfile(['user_id' => $user->id]);
                $fillableProfile = [
                    'group_name','tagline','entity_type','pending_entity_type','entity_type_approved',
                    'location','founded_year','logo_path','about','goals','activities','structure','avatar_color'
                ];
                $profile->fill(Arr::only($profileData, $fillableProfile));
                $profile->user_id = $user->id;
                $profile->save();
            }

            return $user->fresh('politicalProfile');
        });
    }

    public function destroy(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->delete();
        });
    }
}

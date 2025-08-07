<?php

namespace App\Services\PoliticalProfile;

use App\Models\PoliticalProfile;

class PoliticalProfileService
{
    public function storeOrUpdate(array $data, int $userId): PoliticalProfile
    {
        $profile = PoliticalProfile::where('user_id', $userId)->first();

        if ($profile) {
            return app(PoliticalProfileUpdater::class)->updateWithRelations($data, $profile);
        }

        return app(PoliticalProfileCreator::class)->createWithRelations($data, $userId);
    }

    public function getForUser(int $userId): ?PoliticalProfile
    {
        if (!$userId) return null;

        return PoliticalProfile::with(
            'user',
            'links',
            'ideologies',
            'files'
        )->where('user_id', $userId)
            ->first();
    }

    public function listOrganizations(int $perPage = 12)
    {
        return PoliticalProfile::with(
            'links',
            'ideologies',
            'files'
        )->organizations()
            ->latest()
            ->paginate($perPage);
    }
}

<?php

namespace App\Services\PoliticalProfile;

use App\Models\PoliticalProfile;
use App\Models\User;

class PublicProfileService
{
    public function getProfileBySlug(string $slug): ?PoliticalProfile
    {
        $query = PoliticalProfile::query();

        $query->whereHas('user', function ($q) use ($slug) {
            $q->where('slug', $slug);
        });

        $query->with([
            'links',
            'ideologies',
            'files',
            'user'
        ]);

        return $query->first();
    }

    public function getPaginatedPostsBySlug(string $slug, int $perPage = 10)
    {
        return $this->getPostsBySlugAndType($slug, null, $perPage);
    }

    public function getImagePostsBySlug(string $slug, int $perPage = 10)
    {
        return $this->getPostsBySlugAndType($slug, 'image', $perPage);
    }

    public function getVideoPostsBySlug(string $slug, int $perPage = 10)
    {
        return $this->getPostsBySlugAndType($slug, 'video', $perPage);
    }

    public function getFilePostsBySlug(string $slug, int $perPage = 10)
    {
        return $this->getPostsBySlugAndType($slug, 'file', $perPage);
    }

    private function getPostsBySlugAndType(string $slug, ?string $type = null, int $perPage = 10)
    {
        $user = User::where('slug', $slug)->firstOrFail();

        $query = $user->posts()
            ->with('media')
            ->with('likes')
            ->with('comments')
            ->latest();

        if ($type !== null) {
            $query->whereHas('media', function ($q) use ($type) {
                $q->where('type', $type);
            });
        }

        return $query->paginate($perPage);
    }


}

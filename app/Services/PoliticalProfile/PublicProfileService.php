<?php

namespace App\Services\PoliticalProfile;

use App\Models\PoliticalProfile;
use App\Models\User;

class PublicProfileService
{
    public function getProfileBySlug(string $slug): ?PoliticalProfile
    {
        return PoliticalProfile::query()
            ->whereHas('user', fn($q) => $q->where('slug', $slug))
            ->with(['links','ideologies','files','user'])
            ->first();
    }

    public function listProfiles(?array $types, array $filters = [], int $perPage = 12)
    {
        $q = PoliticalProfile::query()
            ->with(['user'])
            ->when($types, fn($qq) => $qq->ofTypes($types));

        if (!empty($filters['q'])) {
            $term = $filters['q'];
            $q->where(function ($qq) use ($term) {
                $qq->where('group_name', 'like', "%$term%")
                    ->orWhere('tagline', 'like', "%$term%")
                    ->orWhere('location', 'like', "%$term%");
            });
        }

        if (!empty($filters['location'])) {
            $q->where('location', 'like', '%'.$filters['location'].'%');
        }

        if (!empty($filters['year_from'])) {
            $q->where('founded_year', '>=', $filters['year_from']);
        }

        if (!empty($filters['year_to'])) {
            $q->where('founded_year', '<=', $filters['year_to']);
        }

        return $q->latest()->paginate($perPage);
    }

    public function listOrganizations(array $filters = [], int $perPage = 12)
    {
        return $this->listProfiles(PoliticalProfile::ORG_TYPES, $filters, $perPage);
    }

    public function listIndividuals(array $filters = [], int $perPage = 12)
    {
        return $this->listProfiles(['individual'], $filters, $perPage);
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
            ->with('media', 'likes', 'comments')
            ->latest();

        if ($type !== null) {
            $query->whereHas('media', fn($q) => $q->where('type', $type));
        }

        return $query->paginate($perPage);
    }
}

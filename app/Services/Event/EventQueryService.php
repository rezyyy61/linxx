<?php

namespace App\Services\Event;

use App\Models\Event\Event;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EventQueryService
{
    public function listPublic(array $f): LengthAwarePaginator
    {
        $q = Event::query()->publicVisible()
            ->with([
                'creator',
                'createdBy:id,name,slug',
                'createdBy.politicalProfile:id,user_id,avatar_color,logo_path',
            ])
            ->search($f['q'] ?? null)
            ->typeIs($f['type'] ?? null)
            ->modeIs($f['mode'] ?? null)
            ->cityIs($f['city'] ?? null)
            ->creatorIs($f['creator_id'] ?? null)
            ->startsBetween($f['from'] ?? null, $f['to'] ?? null)
            ->upcoming($f['upcoming'] ?? true);

        $sort = $f['sort'] ?? 'soon';
        if ($sort === 'latest') $q->orderByDesc('starts_at'); else $q->orderBy('starts_at');

        $per = min(max((int)($f['per_page'] ?? 16), 1), 50);
        return $q->paginate($per)->withQueryString();
    }

    public function findPublic(string $idOrSlug): Event
    {
        return Event::publicVisible()
            ->with([
                'creator',
                'createdBy:id,name,slug',
                'createdBy.politicalProfile:id,user_id,avatar_color,logo_path',
            ])
            ->where(function ($w) use ($idOrSlug) {
                $w->where('slug', $idOrSlug)->orWhere('id', $idOrSlug);
            })
            ->firstOrFail();
    }
}

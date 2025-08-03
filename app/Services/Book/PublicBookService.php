<?php

namespace App\Services\Book;

use App\Models\Book\Book;

class PublicBookService
{
    public function paginate(int $perPage = 10)
    {
        return Book::with('category')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate($perPage);
    }

    public function search(array $filters, int $perPage = 12)
    {
        return Book::query()
            ->with('category')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->when($filters['q'] ?? null, function ($q, $t) {
                $q->where(fn ($s) =>
                $s->where('title','like',"%$t%")
                    ->orWhere('author','like',"%$t%")
                    ->orWhere('description','like',"%$t%"));
            })
            ->when(($filters['category'] ?? 'all') !== 'all',
                fn ($q) => $q->where('category_id', $filters['category']))
            ->when(($filters['pricing']  ?? 'all') !== 'all',
                fn ($q) => $q->where('is_free', $filters['pricing']==='free'))
            ->tap(function ($q) use ($filters) {
                match ($filters['sort_by'] ?? 'newest') {
                    'popular'   => $q->orderByDesc('view_count'),
                    'downloads' => $q->orderByDesc('download_count'),
                    'rating'    => $q->orderByRaw('COALESCE(reviews_avg_rating,0) DESC'),
                    default     => $q->orderByDesc('created_at'),
                };
            })
            ->paginate($perPage);
    }

}

<?php

namespace App\Services\Share\Implementations;

use App\Models\Share\Share;
use App\Models\Share\ShareClick;
use App\Services\Share\Contracts\AnalyticsReporter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentAnalyticsReporter implements AnalyticsReporter
{
    public function clicksSeries(Share $share, Carbon $from, Carbon $to): Collection
    {
        return ShareClick::query()
            ->selectRaw('DATE(clicked_at) as d, COUNT(*) as c')
            ->where('share_id', $share->id)
            ->whereBetween('clicked_at', [$from, $to])
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->map(fn($r) => ['date' => $r->d, 'count' => (int)$r->c]);
    }

    public function topSources(Share $share, int $limit = 10): Collection
    {
        return ShareClick::query()
            ->select('utm_source', DB::raw('COUNT(*) as c'))
            ->where('share_id', $share->id)
            ->whereNotNull('utm_source')
            ->groupBy('utm_source')
            ->orderByDesc('c')
            ->limit($limit)
            ->get()
            ->map(fn($r) => ['source' => $r->utm_source, 'count' => (int)$r->c]);
    }
}

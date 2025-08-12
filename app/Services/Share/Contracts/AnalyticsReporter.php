<?php

namespace App\Services\Share\Contracts;


use App\Models\Share\Share;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

interface AnalyticsReporter
{
    public function clicksSeries(Share $share, Carbon $from, Carbon $to): Collection;
    public function topSources(Share $share, int $limit = 10): Collection;
}

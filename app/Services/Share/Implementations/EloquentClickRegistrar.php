<?php

namespace App\Services\Share\Implementations;


use App\Models\Share\Share;
use App\Models\Share\ShareClick;
use App\Services\Share\Contracts\ClickRegistrar;
use App\Services\Share\DTOs\ClickData;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EloquentClickRegistrar implements ClickRegistrar
{
    public function register(Share $share, ClickData $data): void
    {
        DB::transaction(function () use ($share, $data) {
            $share->increment('clicks_count');

            $host = null;
            $path = null;
            $hash = null;

            if ($data->referrerFull) {
                $host = parse_url($data->referrerFull, PHP_URL_HOST);
                $path = parse_url($data->referrerFull, PHP_URL_PATH);
                $query = parse_url($data->referrerFull, PHP_URL_QUERY);
                if ($query) {
                    $path = ($path ?? '/') . '?' . $query;
                }
                $hash = md5($data->referrerFull, true);
            }

            ShareClick::query()->create([
                'share_id' => $share->id,
                'clicked_at' => Carbon::now(),
                'ip_address' => $data->ip,
                'user_agent' => $data->userAgent,
                'referrer_host' => $host,
                'referrer_path' => $path,
                'referrer_hash' => $hash,
                'utm_source' => $data->utmSource,
                'utm_medium' => $data->utmMedium,
                'utm_campaign' => $data->utmCampaign,
                'country_code' => $data->countryCode,
                'device_type' => $data->deviceType,
                'fingerprint' => $data->fingerprint,
            ]);
        });
    }
}

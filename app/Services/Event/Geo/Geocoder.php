<?php

namespace App\Services\Event\Geo;

use Illuminate\Support\Facades\Http;

class Geocoder
{
    public function geocode(string $query): ?array
    {
        if (!trim($query)) return null;

        $res = Http::withHeaders(['User-Agent' => config('app.name').'/1.0'])
            ->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query,
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => 1,
            ]);

        if (!$res->ok()) return null;
        $arr = $res->json();
        if (!is_array($arr) || empty($arr)) return null;

        $item = $arr[0] ?? [];
        $addr = $item['address'] ?? [];

        return [
            'lat' => isset($item['lat']) ? (float) $item['lat'] : null,
            'lng' => isset($item['lon']) ? (float) $item['lon'] : null,
            'country' => isset($addr['country_code']) ? strtoupper($addr['country_code']) : null,
        ];
    }
}

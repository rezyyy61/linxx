<?php

namespace App\Services\Share\Implementations;

use App\Models\Share\Share;
use App\Models\Share\WebhookEndpoint;
use App\Services\Share\Contracts\WebhookPusher;
use Illuminate\Support\Facades\Http;

class HttpWebhookPusher implements WebhookPusher
{
    public function push(string $event, Share $share, array $payload = []): void
    {
        $endpoints = WebhookEndpoint::query()->where('active', true)->get();
        foreach ($endpoints as $endpoint) {
            $body = [
                'event' => $event,
                'share_id' => $share->id,
                'slug' => $share->slug,
                'occurred_at' => now()->toISOString(),
                'payload' => $payload
            ];
            $signature = $endpoint->secret ? hash_hmac('sha256', json_encode($body), $endpoint->secret) : null;
            $request = Http::withoutVerifying();
            if ($signature) {
                $request = $request->withHeaders(['X-Signature' => $signature]);
            }
            $response = $request->post($endpoint->url, $body);
            $endpoint->last_status_code = $response->status();
            $endpoint->last_delivered_at = now();
            $endpoint->failure_count = $response->successful() ? 0 : $endpoint->failure_count + 1;
            $endpoint->save();
        }
    }
}

<?php

namespace App\Http\Controllers\Share;

use App\Http\Controllers\Controller;
use App\Services\Share\Contracts\ClickRegistrar;
use App\Services\Share\Contracts\ShareResolver;
use App\Services\Share\DTOs\ClickData;
use Illuminate\Http\Request;

class ResolveRedirectController extends Controller
{
    public function __invoke(string $slug, Request $request, ShareResolver $resolver, ClickRegistrar $clicker)
    {
        $result = $resolver->resolveBySlug($slug, $request->input('password'));
        if ($result->status->value !== 'ok') {
            abort(match ($result->status->value) {
                'expired' => 410,
                'revoked' => 403,
                'not_found' => 404,
                'forbidden' => 403,
                default => 404
            });
        }

        $clicker->register($result->share, new ClickData(
            ip: $request->ip(),
            userAgent: $request->userAgent(),
            referrerFull: $request->headers->get('referer'),
            utmSource: $request->query('utm_source'),
            utmMedium: $request->query('utm_medium'),
            utmCampaign: $request->query('utm_campaign'),
        ));

        return response('OK');
    }
}

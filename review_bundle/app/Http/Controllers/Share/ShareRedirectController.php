<?php

namespace App\Http\Controllers\Share;

use App\Http\Controllers\Controller;
use App\Models\Book\Book;
use App\Models\Event\Event;
use App\Models\Post;
use App\Models\Share\Share;
use App\Models\Share\ShareClick;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ShareRedirectController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $share = Share::query()
            ->where('slug', $slug)
            ->with([
                'shareable' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        Post::class  => ['user', 'media'],
                        Event::class => ['user'],
                        Book::class => ['user'],
                    ]);
                },
            ])
            ->firstOrFail();

        if ($this->isRevokedOrExpired($share)) {
            return response()
                ->view('shares.preview', $this->buildErrorMeta($share, 410), 410)
                ->header('X-Robots-Tag', 'noindex, nofollow');
        }

        $isBot = $this->isBot($request->userAgent());

        if ($isBot) {
            $meta = Cache::remember("share_preview:{$slug}", config('share_preview.cache_ttl'), function () use ($share) {
                return app(\App\Services\Share\Preview\SharePreviewBuilder::class)->build($share);
            });

            return response()
                ->view('shares.preview', $meta, 200)
                ->header('X-Frame-Options', 'ALLOWALL');
        }

        $this->logClick($request, $share);

        return redirect()->away($this->targetUrl($share), 302);
    }

    protected function isRevokedOrExpired($share): bool
    {
        return (bool) ($share->revoked_at || ($share->expires_at && now()->greaterThan($share->expires_at)));
    }

    protected function isBot(?string $ua): bool
    {
        if (!$ua) return false;
        $agents = config('share_preview.bot_agents', []);
        $ua = Str::lower($ua);
        foreach ($agents as $needle) {
            if (Str::contains($ua, Str::lower($needle))) return true;
        }
        return false;
    }

    protected function logClick(Request $request, Share $share): void
    {
        try {
            ShareClick::query()->create([
                'share_id'   => $share->id,
                'ip'         => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
                'referrer'   => Str::limit((string) $request->headers->get('referer'), 2048, ''),
                'clicked_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    protected function targetUrl(Share $share): string
    {
        $front = rtrim(config('share_preview.frontend_url'), '/');
        $shareable = $share->shareable;

        if ($shareable instanceof Event) {
            $key  = $shareable->slug ?? $shareable->id;
            $path = trim(config('share_preview.paths.event', '/events'), '/');
            return "{$front}/{$path}/{$key}";
        }

        $key  = $shareable?->slug ?? $shareable?->id ?? $share->shareable_id;
        $path = trim(config('share_preview.paths.post', '/posts'), '/');
        return "{$front}/{$path}/{$key}";
    }

    protected function buildErrorMeta(Share $share, int $status): array
    {
        $front = rtrim(config('share_preview.frontend_url'), '/');
        $shareable = $share->shareable;

        if ($shareable instanceof Event) {
            $key  = $shareable->slug ?? $shareable->id;
            $path = trim(config('share_preview.paths.event', '/events'), '/');
            $canonical = "{$front}/{$path}/{$key}";
        } else {
            $key  = $shareable?->slug ?? $shareable?->id ?? $share->shareable_id;
            $path = trim(config('share_preview.paths.post', '/posts'), '/');
            $canonical = "{$front}/{$path}/{$key}";
        }

        return [
            'status'       => $status,
            'url'          => url()->current(),
            'canonical'    => $canonical,
            'title'        => 'Link unavailable',
            'description'  => 'This share link is expired or revoked.',
            'image'        => config('share_preview.default_image'),
            'site_name'    => config('app.name'),
            'twitter_card' => 'summary_large_image',
            'noindex'      => true,
        ];
    }
}

<?php

namespace App\Services\Share\Preview;

use App\Models\Share\Share;
use Illuminate\Support\Str;

class SharePreviewBuilder
{
    public function build(Share $share): array
    {
        $front = rtrim(config('share_preview.frontend_url'), '/');
        $site = config('app.name');
        $post = $share->shareable;

        $title = $this->titleFrom($post, $site);
        $description = $this->descriptionFrom($post);
        $image = $this->imageFrom($post) ?: config('share_preview.default_image');

        $canonical = $front.'/posts/'.($post->id ?? $share->shareable_id);

        return [
            'status' => 200,
            'url' => url()->current(),
            'canonical' => $canonical,
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'site_name' => $site,
            'twitter_card' => 'summary_large_image',
            'noindex' => false,
        ];
    }

    protected function titleFrom($post, string $site): string
    {
        $who = optional($post->user)->name ?: '@user';
        $excerpt = $this->plainExcerpt(optional($post)->text);
        if ($excerpt) {
            return "{$who}: {$excerpt}";
        }
        return "{$who} on {$site}";
    }

    protected function descriptionFrom($post): string
    {
        $text = (string) optional($post)->text;
        $plain = $this->plain($text);
        return Str::limit($plain, 200, '…');
    }

    protected function imageFrom($post): ?string
    {
        if (!$post) return null;

        // 추측이야: اگر از رسانه spatie استفاده می‌کنی:
        if (method_exists($post, 'getFirstMediaUrl')) {
            $url = $post->getFirstMediaUrl('images');
            if ($url) return $url;
            $thumb = $post->getFirstMediaUrl('thumb');
            if ($thumb) return $thumb;
        }

        // 추측ی دیگر: اگر آرایه media داری:
        if (isset($post->media) && is_iterable($post->media)) {
            foreach ($post->media as $m) {
                $type = $m->type ?? ($m['type'] ?? null);
                if ($type === 'image') {
                    return $m->url ?? ($m['url'] ?? null);
                }
                if ($type === 'video' && !empty($m->thumbnail_url ?? $m['thumbnail_url'] ?? null)) {
                    return $m->thumbnail_url ?? $m['thumbnail_url'];
                }
            }
        }

        return null;
    }

    protected function plain(string $html = null): string
    {
        $html = $html ?? '';
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    protected function plainExcerpt(?string $html): ?string
    {
        $p = $this->plain($html ?? '');
        $p = trim($p);
        return $p !== '' ? Str::limit($p, 60, '…') : null;
    }
}

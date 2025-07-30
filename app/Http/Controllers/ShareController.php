<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShareController extends Controller
{
    public function show($id, Request $request)
    {
        $post = Post::with('media')->findOrFail($id);

        // تصویری که در کارت استفاده می‌شود
        $image = $post->media->firstWhere('type', 'image')?->full_url
            ?? $post->media->firstWhere('type', 'video')?->full_poster
            ?? asset('default-post.jpg');

        /*--------------------------------------------------------------
         | ۱) تشخیص خزنده (ربات)
         |--------------------------------------------------------------*/
        $ua   = Str::lower($request->userAgent() ?? '');
        $bots = [
            'facebookexternalhit',   // Facebook
            'twitterbot',            // Twitter/X
            'whatsapp',              // WhatsApp
            'telegrambot',           // Telegram
            'linkedinbot',           // LinkedIn
            'slackbot',              // Slack
            'discordbot',            // Discord
            'pinterest',             // Pinterest
            'googlebot',             // Google (برای احتیاط)
        ];

        $isBot = collect($bots)->contains(fn ($b) => Str::contains($ua, $b));
        $debug = $request->boolean('debug');   // ?debug=1 برای تست دستی

        /*--------------------------------------------------------------
         | ۲) پاسخ مناسب
         |--------------------------------------------------------------*/
        if ($isBot || $debug) {
            // ویو شامل متا‑تگ‌ها
            return response()
                ->view('share.post', compact('post', 'image'))
                ->header('Content-Type', 'text/html; charset=UTF-8');
        }

        // کاربر عادی → ریدایرکت 302 (موقت) به صفحه واقعی پست
        return redirect()->route('posts.show', $post);
    }
}

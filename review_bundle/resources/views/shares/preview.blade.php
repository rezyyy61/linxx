<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? config('app.name') }}</title>

    <meta property="og:type" content="article">
    <meta property="og:site_name" content="{{ $site_name ?? config('app.name') }}">
    <meta property="og:url" content="{{ $url ?? request()->fullUrl() }}">
    <meta property="og:title" content="{{ $title ?? '' }}">
    <meta property="og:description" content="{{ $description ?? '' }}">
    @if(!empty($image))
    <meta property="og:image" content="{{ $image }}">
    @endif

    <meta name="twitter:card" content="{{ $twitter_card ?? 'summary_large_image' }}">
    <meta name="twitter:title" content="{{ $title ?? '' }}">
    <meta name="twitter:description" content="{{ $description ?? '' }}">
    @if(!empty($image))
    <meta name="twitter:image" content="{{ $image }}">
    @endif

    @if(!empty($canonical))
    <link rel="canonical" href="{{ $canonical }}">
    @endif

    @if(!empty($noindex))
    <meta name="robots" content="noindex, nofollow">
    @endif

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        html,body{margin:0;padding:0;background:#0b0b0b;color:#eee;font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,"Helvetica Neue",Arial}
        .wrap{display:flex;min-height:100vh;align-items:center;justify-content:center;padding:24px}
        .card{max-width:720px;width:100%;background:#121212;border:1px solid #222;border-radius:16px;padding:20px}
        .title{font-weight:700;font-size:18px;margin:0 0 8px}
        .desc{opacity:.8;line-height:1.5;font-size:14px}
        .thumb{width:100%;height:auto;border-radius:12px;margin:12px 0}
        .cta{display:inline-block;margin-top:8px;color:#9ecbff;text-decoration:none}
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        @if(!empty($image))
        <img class="thumb" src="{{ $image }}" alt="preview">
        @endif
        <h1 class="title">{{ $title ?? '' }}</h1>
        <p class="desc">{{ $description ?? '' }}</p>
        @if(!empty($canonical))
        <a class="cta" href="{{ $canonical }}">Open post</a>
        @endif
    </div>
</div>
</body>
</html>

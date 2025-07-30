<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $post->title ?? 'New Post' }} • MyAwesomeSite</title>

    <link rel="canonical" href="{{ url('share/posts/' . $post->id) }}">

    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $post->title ?? 'New Post' }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($post->text), 150) }}">
    <meta property="og:image" content="{{ $image ?: asset('images/og-default.jpg') }}">
    <meta property="og:url" content="{{ url('/posts/' . $post->id) }}">
    <meta property="og:site_name" content="MyAwesomeSite">
    <meta property="og:locale" content="en_US">
    <meta property="og:updated_time" content="{{ $post->updated_at?->toW3cString() }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $post->title ?? 'New Post' }}">
    <meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($post->text), 150) }}">
    <meta name="twitter:image" content="{{ $image ?: asset('images/og-default.jpg') }}">
    <meta name="twitter:site" content="@MyAwesomeSite">
    @isset($post->user->twitter)
        <meta name="twitter:creator" content="@{{ $post->user->twitter }}">
    @endisset

    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
</head>
<body class="bg-gray-50 dark:bg-neutral-900 text-gray-800 dark:text-gray-100 flex items-center justify-center min-h-screen p-4">
<main class="text-center space-y-6 max-w-md w-full">
    @if(request()->has('debug'))
        <div class="px-4 py-2 rounded-lg bg-green-100 text-green-800 font-semibold shadow">
            DEBUG MODE: No redirect — meta tags loaded.
        </div>
    @endif

    <img src="{{ asset('images/logo.svg') }}" alt="Brand logo" class="h-14 mx-auto {{ request()->has('debug') ? '' : 'animate-pulse duration-700' }}">

    <h1 class="text-2xl sm:text-3xl font-extrabold leading-tight">
        {{ $post->title ?? 'New Post' }}
    </h1>

    <p class="text-gray-600 dark:text-gray-400 line-clamp-3">
        {{ \Illuminate\Support\Str::limit(strip_tags($post->text), 120) }}
    </p>

    <a href="{{ route('posts.show', $post) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-lg transition-all" rel="noopener">
        {{ __('Read on MyAwesomeSite') }}
        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M12.293 2.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414L13 12.414a1 1 0 11-1.414-1.414L14.586 8H7a1 1 0 110-2h7.586L12.293 3.707a1 1 0 010-1.414z"/></svg>
    </a>

    @unless(request()->has('debug'))
        <noscript>
            <meta http-equiv="refresh" content="0;url={{ route('posts.show', $post) }}">
        </noscript>
        <p class="text-sm text-gray-500 dark:text-gray-500">
            {{ __('If you are not redirected automatically, click the button above.') }}
        </p>
    @endunless
</main>
</body>
</html>

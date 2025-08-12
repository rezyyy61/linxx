<?php

return [
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:8081'),
    'paths' => [
        'post'  => '/posts',
        'event' => '/events',
        'book'  => '/books',
    ],
    'cache_ttl' => 300,
    'default_image' => env('SHARE_DEFAULT_IMAGE'),
    'bot_agents' => ['facebookexternalhit', 'twitterbot', 'slackbot', 'TelegramBot', 'LinkedInBot', 'Googlebot'],
];

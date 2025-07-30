<?php

return [

    'paths' => [
        'api/*',
        'broadcasting/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
        'register',
        'user',
    ],
    'allowed_origins' => ['*'],

    'allowed_methods' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];

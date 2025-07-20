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
    'allowed_origins' => [
        'http://localhost:8081',
        'http://192.168.1.193:8081'
    ],

    'allowed_methods' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];

<?php

return [

    'paths' => ['api/*', 'login', 'logout', 'sanctum/csrf-cookie', 'register', 'user'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:8080',
        'http://localhost:8081',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];

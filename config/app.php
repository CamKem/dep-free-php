<?php

configure(static fn() => [
    'app' => [
        'env' => env('APP_ENV', 'development'),
        'name' => env('APP_NAME', 'Camwork'),
        'debug' => env('APP_DEBUG', true),
        'url' => filter_var(env('APP_URL', 'http://camwork.test'), FILTER_VALIDATE_URL),
    ],
]);

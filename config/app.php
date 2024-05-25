<?php

configure(fn() => [
    'env' => env('APP_ENV', 'local'),
    'name' => env('APP_NAME', 'Camwork'),
    'debug' => env('APP_DEBUG', true),
    'url' => filter_var(
        env('APP_URL', 'http://camwork.test'),
        FILTER_VALIDATE_URL
    ),
]);

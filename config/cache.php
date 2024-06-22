<?php

// cache configuration

configure(fn() => [
    'driver' => env('CACHE_DRIVER', 'file'),
    'file' => [
        'path' => BASE_PATH . 'storage/cache',
        'lifetime' => env('CACHE_LIFETIME', 3600),
        'prefix' => env('CACHE_PREFIX', 'cache_'),
        'extension' => '.cache'
    ],
    'database' => [
        'table' => 'cache',
        'lifetime' => env('CACHE_LIFETIME', 3600),
        'prefix' => env('CACHE_PREFIX', 'cache_'),
    ],
]);

<?php

// cache configuration

configure(fn() => [
    'driver' => env('CACHE_DRIVER', 'file'),
    'file' => [
        'path' => BASE_PATH . 'storage/cache',
        'lifetime' => 3600,
        'prefix' => 'cache_',
        'extension' => '.cache'
    ],
    'database' => [
        'table' => 'cache',
        'lifetime' => 3600,
        'prefix' => 'cache_',
    ],
]);

<?php

configure(fn() => [
    'database' => [
        'host' => env('DB_HOST', 'localhost'),
        'port' => env('DB_PORT', '3306'),
        'user' => env('DB_USER', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'dbname' => env('DB_NAME', strtolower(str_replace(' ', '_', env('APP_NAME', 'camwork')))),
        'charset' => env('DB_CHARSET', 'utf8mb4'),
        'options' => [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    ],
]);
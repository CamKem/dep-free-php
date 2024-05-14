<?php

configure(static fn() => [
    'template' => [
        'paths' => [
            'views' => 'templates',
            'assets' => 'public',
        ],
        'layout' => 'layouts.app',
    ],
]);
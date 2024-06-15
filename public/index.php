<?php

const BASE_PATH = __DIR__.'/../';
include BASE_PATH . 'bootstrap/functions.php';

spl_autoload_register(static function ($class) {
    $class = preg_replace('/^App/', 'app', $class);
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    require BASE_PATH . "{$class}.php";
});

require include_path('bootstrap/bootstrap.php');
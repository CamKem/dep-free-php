<?php

const BASE_PATH = __DIR__.'/../';
include BASE_PATH . 'bootstrap/functions.php';

spl_autoload_register(static function ($class) {
    $class = preg_replace('/^App\\\\/', 'app/', $class);
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    $file = BASE_PATH . "{$class}.php";

    if (file_exists($file)) {
        require $file;
    } else {
        $error = "Autoload Error: Unable to load class: {$class} from file {$file}";
        error_log($error);
    }
});

require include_path('bootstrap/bootstrap.php');
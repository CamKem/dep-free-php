<?php

const BASE_PATH = __DIR__.'/../';
include BASE_PATH . 'bootstrap/functions.php';

spl_autoload_register(static function ($class) {
    $baseNamespace = 'App\\';

    if (str_starts_with($class, $baseNamespace)) {
        $relativeClass = substr($class, strlen($baseNamespace));
        $file = BASE_PATH . 'app/' . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
        } else {
            logger("Class $class not found at $file");
        }
    }
});

require include_path('bootstrap/bootstrap.php');
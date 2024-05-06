<?php

namespace App\Core;

use RuntimeException;
use Throwable;

class View
{

    protected static array $sharedData = [];

    public static function make(string $view, array $data = []): self
    {
        self::share('view', $view);
        $data = array_merge(static::$sharedData, $data);
        self::share('title', $data['title'] ?? '');
        extract($data, \EXTR_SKIP);
        ob_start();
        try {
            require_once base_path(
                'views/'
                . str_replace('.', '/', $view)
                . '.view.php'
            );
            $content = ob_get_clean();
            require_once base_path('views/layouts/app.view.php');
        } catch (Throwable $e) {
            //ob_end_clean();
            throw new RuntimeException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        } finally {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
        }
        return new static($content);
    }

    public static function include(string $view, array $data = []): void
    {
        $data = array_merge(static::$sharedData, $data);
        extract($data, \EXTR_OVERWRITE);
        require_once base_path(
            'views/'
            . str_replace('.', '/', $view)
            . '.view.php'
        );
    }

    public static function share(string $key, mixed $value): void
    {
        static::$sharedData[$key] = $value;
    }

}
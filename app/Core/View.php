<?php

namespace App\Core;

use RuntimeException;
use Throwable;

class View
{

    private array $data = [];
    protected string $view;
    private bool $isNested = false;

    public function __get(string $key)
    {
        return $this->data[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    public function __isset(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public static function make(string $view, array $data = []): self
    {
        logger('Creating view: ' . $view);
        $instance = new static();
        $instance->data = $data;
        $instance->view = $view;
        $instance->render();
        return $instance;
    }

    public static function add(string $view, array $data = []): string
    {
        $instance = new static();
        $instance->isNested = true;
        $instance->data = $data;
        $instance->view = $view;
        return (string)$instance;
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function render(): string
    {
        logger('Rendering view: ' . $this->view);
        ob_start();
        try {
            if ($this->isNested) {
                $this->require($this->view);
                $this->content = ob_get_clean();
                return $this->content;
            }
            $this->require($this->view);
            $this->content = ob_get_clean();
            $this->require('layouts.app');
            return ob_get_clean();
        } catch (Throwable $e) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            $this->message = $e->getMessage();
            $this->require('error.exception');
            $this->content = ob_get_clean();
            $this->require('layouts.app');
            return ob_get_clean();
        } finally {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
        }
    }

    private function require(string $view): void
    {
        try {
            if (!empty($this->data)) {
                extract($this->data, \EXTR_SKIP);
            }
            require_once base_path(
                'views/'
                . str_replace('.', '/', $view)
                . '.view.php'
            );
        } catch (Throwable $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

}
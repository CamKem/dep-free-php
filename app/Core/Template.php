<?php

namespace App\Core;

use RuntimeException;

class Template
{
    private array $variables = [];
    private mixed $templateDir;
    private mixed $layout;
    private $content;
    private bool $isNestedView = false;

    public function __construct()
    {
        $this->templateDir = config('template.paths.views');
        $this->layout = config('template.layout');
    }

    public function set($name, $value): self
    {
        $this->variables[$name] = $value;
        return $this;
    }

    public function content($templateFile): self
    {
        $this->content = $templateFile;
        return $this;
    }

    public function render(): string
    {
        $content = $this->compileTemplate($this->content);
        if (!$this->isNestedView && $this->layout) {
            $layout = $this->compileTemplate($this->layout);
            return str_replace('{{ slot }}', $content, $layout);
        }
        return $content;
    }

    public static function make($file, ?array $variables = null, bool $nested = false): self
    {
        $variables ??= [];
        $view = new self();
        $view->isNestedView = $nested;
        $view->content($file);
        foreach ($variables as $key => $value) {
            $view->set($key, $value);
        }
        if (!$nested) {
            echo $view->render();
        }
        return $view;
    }

//    public static function add($file, $variables = []): string
//    {
//        $nestedView = new self();
//        $nestedView->isNestedView = true;
//        $nestedView->content($file);
//        foreach ($variables as $key => $value) {
//            $nestedView->set($key, $value);
//        }
//        return $nestedView->render();
//    }


    private function compileTemplate($templateFile): string
    {
        $filePath = $this->createTemplatePath($templateFile);
        if (!file_exists($filePath)) {
            throw new RuntimeException("Template file {$filePath} not found");
        }

        ob_start();
        extract($this->variables, \EXTR_SKIP);
        require_once($filePath);
        return ob_get_clean();
    }

    private function createTemplatePath($templateFile): string
    {
        return base_path(
            $this->templateDir
            . DIRECTORY_SEPARATOR
            . str_replace('.', '/', $templateFile)
            . '.view.php'
        );
    }
}

//
//namespace App\Core;
//
//use RuntimeException;
//use Throwable;
//
//class Template
//{
//
//    private array $data = [];
//    protected string $view;
//    private bool $isNested = false;
//
//    public function __get(string $key)
//    {
//        return $this->data[$key] ?? null;
//    }
//
//    public function __set(string $key, mixed $value): void
//    {
//        $this->data[$key] = $value;
//    }
//
//    public function __isset(string $key): bool
//    {
//        return isset($this->data[$key]);
//    }
//
//    public static function make(string $view, array $data = []): self
//    {
//        $instance = new static();
//        $instance->data = $data;
//        $instance->view = $view;
//        $instance->render();
//        return $instance;
//    }
//
//    public static function add(string $view, array $data = []): string
//    {
//        $instance = new static();
//        $instance->isNested = true;
//        $instance->data = $data;
//        $instance->view = $view;
//        return $instance->render();
//    }
//
//    public function render(): string
//    {
//        ob_start();
//        try {
//            if ($this->isNested) {
//                $this->require($this->view);
//                return ob_get_contents();
//            }
//            $this->require($this->view);
//            $this->content = ob_get_clean();
//            $this->title = $this->title ?? 'Error';
//            $this->require('layouts.app');
//            return ob_get_clean();
//        } catch (Throwable $e) {
//            if (ob_get_level() > 0) {
//                ob_end_clean();
//            }
//            $this->message = $e->getMessage();
//            $this->require('errors.exception');
//            $this->content = ob_get_clean();
//            $this->title = 'Exception';
//            $this->require('layouts.app');
//            return ob_get_clean();
//        } finally {
//            if (ob_get_level() > 0) {
//                ob_end_clean();
//            }
//        }
//    }
//
//    private function require(string $view): void
//    {
//        try {
//            if (!empty($this->data)) {
//                extract($this->data, \EXTR_SKIP);
//            }
//            require_once base_path(
//                'templates/'
//                . str_replace('.', '/', $view)
//                . '.view.php'
//            );
//        } catch (Throwable $e) {
//            throw new RuntimeException($e->getMessage());
//        }
//    }
//
//}
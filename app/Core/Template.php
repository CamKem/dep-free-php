<?php
namespace App\Core;

use RuntimeException;

class Template
{
    private array $variables = [];
    private mixed $templateDir;
    private static mixed $layout;
    private string $content;
    private bool $isNestedView = false;

    public function __construct()
    {
        $this->templateDir = config('template.paths.views');
        self::$layout = self::$layout ?? config('template.layout');
    }

    public static function layout($layout): void
    {
        self::$layout = $layout;
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
        if (!$this->isNestedView && self::$layout) {
            $layout = $this->compileTemplate(self::$layout);
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
        return include_path(
            $this->templateDir
            . DIRECTORY_SEPARATOR
            . str_replace('.', '/', $templateFile)
            . '.view.php'
        );
    }
}

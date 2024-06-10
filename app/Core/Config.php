<?php

namespace App\Core;

use ReflectionFunction;
use RuntimeException;

class Config
{
    protected array $config = [];

    protected static array $configClosures = [];

    public static function storeConfigClosure(callable $configClosure): callable
    {
        $reflection = new ReflectionFunction($configClosure);
        $fileName = basename($reflection->getFileName(), '.php');
        return self::$configClosures[$fileName] = $configClosure;
    }

    public function loadConfig(): array
    {
        $configPaths = glob(include_path('config/*.php'));
        if (empty($configPaths)) {
            throw new RuntimeException("Failed to find config files");
        }

        return $this->loadConfigFiles($configPaths);
    }

    protected function loadConfigFiles(array $filePaths): array
    {
        foreach ($filePaths as $filePath) {
            require $filePath;
        }
        return $this->loadConfigValues();
    }

    public function loadConfigValues(): array
    {
        foreach (self::$configClosures as $key => $configClosure) {
            $this->set($key, $configClosure());
        }

        return $this->config;
    }

    public function get(string $target): mixed
    {
        $keys = explode('.', $target);
        $value = $this->config;

        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                return null;
            }
            $value = $value[$key];
        }

        return $value;
    }

    private function set(string $key, mixed $value): void
    {
        $this->config[$key] = $value;
    }

}

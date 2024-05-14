<?php

namespace App\Core;

use RuntimeException;

class Config
{
    protected array $config = [];

    protected static array $configClosures = [];

    public static function storeConfigClosure(callable $configClosure): callable
    {
        return self::$configClosures[] = $configClosure;
    }

    public function loadConfig(): array
    {
        $configPaths = glob(base_path('config/*.php'));
        if (empty($configPaths)) {
            throw new RuntimeException("Failed to find config files");
        }

        return $this->loadConfigFiles($configPaths);
    }

    protected function loadConfigFiles(array $filePaths): array
    {
        foreach ($filePaths as $filePath) {
            // TODO: extract the file name from the path, and use it for the key in the array
            //  so we don't have to nest the name in the array in the config file
            require $filePath;
        }

        return $this->loadConfigValues();
    }

    public function loadConfigValues(): array
    {
        $values = [];
        foreach (self::$configClosures as $configClosure) {
            $values = array_merge($values ?? [], $configClosure($values ?? []));
            $configClosure($values);
        }

        return $this->config = $values;
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

    public function set(string $key, mixed $value): void
    {
        $this->config[$key] = $value;
    }

}

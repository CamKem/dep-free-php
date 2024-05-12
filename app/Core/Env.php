<?php

namespace App\Core;

use RuntimeException;

class Env
{
    protected array $env = [];

    public function load(): void
    {
        if (empty($this->env)) {
            $envFilePath = base_path('.env');

            if (!file_exists($envFilePath) || !is_readable($envFilePath)) {
                throw new RuntimeException("Environment file not found or unreadable.");
            }

            if (!is_readable($envFilePath)) {
                throw new RuntimeException("Environment file is not readable.");
            }

            $env = parse_ini_file($envFilePath);
            if ($env === false) {
                throw new RuntimeException("Failed to parse the environment file.");
            }

            $this->env = $env;
        }

    }

    public function get(string $key, $default = null): string
    {
        return $this->env[$key] ?? $default;
    }

}
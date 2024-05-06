<?php

namespace App\Services;

use App\Core\Config;
use App\Core\ServiceProvider;
use Override;

class ConfigService extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    #[Override]
    public function register(): void
    {
        $this->app->singleton(Config::class);
    }

    /**
     * Boot the service provider.
     *
     * @uses Config::loadConfig
     * @uses Config::set
     */
    #[Override]
    public function boot(): void
    {
        $config = $this->app->resolve(Config::class);
        $values = $config->loadConfig();
        foreach ($values as $key => $value) {
            $config->set($key, $value);
        }
    }

}
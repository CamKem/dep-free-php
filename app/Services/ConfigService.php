<?php

namespace App\Services;

use App\Core\Config;
use App\Core\ServiceProvider;
use Override;

class ConfigService extends ServiceProvider
{

    #[Override]
    public function register(): void
    {
        $this->app->singleton(Config::class);
    }

    #[Override]
    public function boot(): void
    {
        /** @var Config $config */
        $config = $this->app->resolve(Config::class);
        $config->loadConfig();
    }

}
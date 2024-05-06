<?php

namespace App\Services;

use App\Core\Env;
use App\Core\ServiceProvider;
use Override;

class EnvService extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->singleton(Env::class);
    }

    /**
     * Load the environment variables
     * @uses Env::load
     */
    #[Override]
    public function boot(): void
    {
        $this->app->resolve(Env::class)->load();
    }

}
<?php

namespace App\Services;

use App\Core\Routing\Router;
use App\Core\ServiceProvider;
use Override;

class RouterService extends ServiceProvider
{

    #[Override]
    public function register(): void
    {
        $this->app->singleton(Router::class);
    }

    /**
     * Load the Routes into the RouteCollection in the Router
     * @uses Router::loadRoutes()
     */
    #[Override]
    public function boot(): void
    {
        /** @var Router $router */
        $router = $this->app->resolve(Router::class);
        $router->loadRoutes();
    }

}
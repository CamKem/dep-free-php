<?php

namespace App\Services;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Routing\Router;
use App\Core\ServiceProvider;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\PreviousUrlMiddleware;
use Override;

class MiddlewareService extends ServiceProvider
{

    private array $middleware = [
        'auth' => AuthMiddleware::class,
        'guest' => GuestMiddleware::class,
        'admin' => AdminMiddleware::class,
        'previous.url' => PreviousUrlMiddleware::class
    ];

    #[Override]
    public function register(): void
    {
        foreach ($this->middleware as $alias => $middleware) {
            $this->app->alias($alias, $middleware);
        }
    }

    #[Override]
    public function boot(): void
    {
        /**
         * Register request middleware on the router
         * @see Router::registerGlobalMiddleware
         */
        $this->app->resolve(Router::class)->registerGlobalMiddleware([
            $this->app->resolve('previous.url')
        ]);

        /**
         * Register a shutdown function to apply response middleware
         */
        register_shutdown_function(fn() => $this->applyResponseMiddleware());
    }

    private function applyResponseMiddleware(): void
    {
        $request = $this->app->resolve(Request::class);
        $response = $this->app->resolve(Response::class);
        /** @var Router $router */
        $router = $this->app->resolve(Router::class);
        foreach ($router->getResolvedMiddleware() as $middleware) {
            if (method_exists($middleware, 'after')) {
                $middleware->after($request, $response);
            }
        }
    }

}
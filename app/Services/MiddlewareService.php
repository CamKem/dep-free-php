<?php

namespace App\Services;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Routing\Router;
use App\Core\ServiceProvider;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\PreviousUrlMiddleware;
use Override;

class MiddlewareService extends ServiceProvider
{

    private array $requestMiddleware = [
        'auth' => AuthMiddleware::class,
        'guest' => GuestMiddleware::class
    ];

    private array $responseMiddleware = [
        'previous.url' => PreviousUrlMiddleware::class
    ];

    #[Override]
    public function register(): void
    {
        // Merge and register both request and response middleware
        $middlewares = array_merge($this->requestMiddleware, $this->responseMiddleware);
        foreach ($middlewares as $alias => $middleware) {
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
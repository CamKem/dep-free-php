<?php

namespace App\Core\Routing;

/**
 * RouteProxy: static proxy for the Route Registrar
 * Used in registering routes in the application
 *
 * @method static get(string $uri)
 * @method static post(string $uri)
 * @method static put(string $uri)
 * @method static patch(string $uri)
 * @method static delete(string $uri)
 * @method static options(string $uri)
 * @method static any(string $uri)
 */
class RouteProxy
{
    protected static Router $router;

    public static function __callStatic($method, $parameters)
    {
        if (!isset(self::$router)) {
            self::$router = app()->resolve(Router::class);
        }
        return (new RouteRegistrar(self::$router))->{$method}(...$parameters);
    }

}
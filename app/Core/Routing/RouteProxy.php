<?php

namespace App\Core\Routing;

use App\Core\App;

/**
 * RouteProxy
 *
 * This class is a proxy for the RouteRegistrar class. It allows you to call methods on the RouteRegistrar class
 * statically, without having to instantiate the RouteRegistrar class. This is useful because the RouteRegistrar
 * class is used to define routes in the routes/web.php file, and the routes/web.php file is loaded before the
 * App/Container is instantiated. This means that we can't use dependency injection to inject the Router instance
 * into the RouteRegistrar class. Instead, we use the RouteProxy class to create a new instance of the RouteRegistrar
 * class and call methods on it statically.
 *
 * @method static get(string $uri, Callable|array|null $action = null)
 * @method static post(string $uri, Callable|array|null $action = null)
 * @method static put(string $uri, Callable|array|null $action = null)
 * @method static patch(string $uri, Callable|array|null $action = null)
 * @method static delete(string $uri, Callable|array|null $action = null)
 * @method static options(string $uri, Callable|array|null $action = null)
 * @method static any(string $uri, Callable|array|null $action = null)
 */
class RouteProxy
{
    protected static Router $router;

    public static function __callStatic($method, $parameters)
    {
        if (!isset(self::$router)) {
            self::$router = App::getContainer()->resolve(Router::class);
        }
        return (new RouteRegistrar(self::$router))->{$method}(...$parameters);
    }

}
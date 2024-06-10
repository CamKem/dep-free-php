<?php

namespace App\Core\Routing;

use App\Core\Collecting\Collection;
use App\Core\Http\Request;

class RouteCollection
{
    /**
     * Array of routes keyed by name.
     */
    protected Collection $routes;

    public function __construct()
    {
        $this->routes = new Collection();
    }

    /**
     * Add the given route to the arrays of routes.
     *
     * @param Route $route
     * @return Route
     */
    public function add(Route $route): Route
    {
        $this->routes->put($route->getName(), $route);

        return $route;
    }

    /**
     * Find the first route matching a given request.
     *
     * @param Request $request
     * @return Route|null
     */
    public function match(Request $request): ?Route
    {
        foreach ($this->routes->toArray() as $route) {
            if ($route->matches(
                strtolower($request->getMethod()), $request->getUri()
            )) {
                return $route;
            }
        }
        return null;
    }

    /**
     * Determine if the route collection contains a given named route.
     * @param string $name
     * @return bool
     */
    public function hasRoute(string $name): bool
    {
        return $this->routes->has($name);
    }

    /**
     * Get a route instance by its name
     *
     * @param string $name
     * @return Route|null
     */
    public function getRoute(string $name): Route|null
    {
        return $this->routes->get($name);
    }

    /**
     * Get all the routes that are defined.
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes->toArray();
    }

}
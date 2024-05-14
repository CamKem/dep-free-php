<?php

namespace App\Core\Routing;

use App\Core\Exceptions\RouteException;
use App\Core\Http\Request;
use App\Core\Template;
use Closure;

class Router
{
    protected RouteCollection $routes;

    public function __construct()
    {
        $this->routes = new RouteCollection();
    }

    public function addRoute(Route $route): Route
    {
        return $this->routes->add($route);
    }

    public function dispatch(Request $request): mixed
    {
        // Check for form spoofing
        $this->checkSpoofedForm($request);

        // Match the request to a route
        $route = $this->getRoutes()->match($request);

        if ($route === null) {
            return $this->abort();
        }

        // Apply the middleware to the route
        $this->applyMiddleware($request, $route);

        // Now the request has been matched to a route.
        // We should store the route parameters in the request object
        $request->setRouteParameters();

        $controller = $route->getController();
        $action = $route->getAction();

        // if $action is null, then we can call the invoke method on the controller
        if ($action === null) {
            return (new $controller())($request);
        }

        // if $controller is instance of Closure, then we can call it directly
        if ($controller instanceof Closure) {
            return $controller($request);
        }

        // if $controller is a string, then we can call the method on the controller
        if (is_string($controller)) {
            return (new $controller)->$action($request);
        }
    }

    protected function applyMiddleware(Request $request, Route $route): void
    {
        foreach ($route->getMiddleware() as $alias) {
            if (app()->hasAlias($alias)) {
                $middleware = app()->getAlias($alias);
                (new $middleware())->handle($request);
            }
        }
    }

    public function getRoutes(): RouteCollection
    {
        return $this->routes;
    }

    public function getRoute(string $name): Route|null
    {
        return $this->routes->getRoute($name);
    }

    // generate a URL for the given named route
    public function generate(string $name, array $params = []): string
    {
        $route = $this->routes->getRoute($name);
        if ($route === null) {
            throw new RouteException("Route {$name} not found.");
        }
        return $route->generate($params);
    }

    /**
     * Load the Routes into the RouteCollection in the Router
     */
    public function loadRoutes(): void
    {
        require base_path('routes/web.php');
    }

    /**
     * Abort the request & Return the error page
     * @param int $code
     * @return Template
     */
    protected function abort(int $code = 404): Template
    {
        http_response_code($code);
        return view("errors.{$code}", ['title' => $code]);
    }

    /**
     * Override the request method if form spoofing is detected
     * @param Request $request
     * @return void
     */
    public function checkSpoofedForm(Request $request): void
    {
        if ($request->getMethod() === 'POST' && $request->has('_method')) {
            // Validate the _method field
            $method = strtoupper($request->get('_method'));
            if (in_array($method, ['PUT', 'PATCH', 'DELETE'])) {
                // Use the _method field as the HTTP method
                $request->setMethod($method);
            }
        }
    }
}

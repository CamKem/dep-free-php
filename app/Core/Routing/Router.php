<?php

namespace App\Core\Routing;

use App\Core\Exceptions\RouteException;
use App\Core\Http\Request;
use App\Core\Middleware;
use App\Core\Template;
use Closure;

class Router
{
    protected array $globalMiddleware = [];
    private array $resolvedMiddleware = [];
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

        // resolve the route & global middleware
        $this->resolveMiddleware($route);

        // Apply the middleware stack
        $this->applyMiddleware($request, $this->resolvedMiddleware);

        // Now the request has been matched to a route.
        // We should store the route parameters in the request object
        $request->setRouteParameters();

        $controller = $route->getController();
        $action = $route->getAction();

        // if $controller is an instance of Closure, then we can call it directly
        if ($controller instanceof Closure) {
            return $controller($request);
        }

        // if $action is null, then we can call the invoke method on the controller
        if ($action === null) {
            return (new $controller())($request);
        }

        // if $controller is a string, then we can call the method on the controller
        if (is_string($controller)) {
            return (new $controller)->$action($request);
        }
    }

    protected function resolveMiddleware(Route $route): void
    {
        $resolvedMiddleware = array_map(static function ($alias) {
            return app()->resolve($alias);
        }, $route->getMiddleware());

        // merge the resolve route middleware with the global middleware
        $this->resolvedMiddleware = array_merge($this->globalMiddleware, $resolvedMiddleware);
    }

    protected function applyMiddleware(Request $request, array $stack): mixed
    {
        $stackPointer = 0;

        $next = function ($request) use (&$stack, &$stackPointer) {
            $middleware = $stack[$stackPointer];
            $stackPointer++;
            return $middleware->handle($request, $this->getNextMiddleware($stack, $stackPointer));
        };

        return $next($request);
    }

    private function getNextMiddleware(array $stack, int $stackPointer): Closure
    {
        return function ($request) use (&$stackPointer, &$stack) {
            if ($stackPointer < count($stack)) {
                $middleware = $stack[$stackPointer];
                $stackPointer++;
                return $middleware->handle($request, $this->getNextMiddleware($stack, $stackPointer));
            }

            // When no more middleware return request wrapped in closure
            return static fn(Request $request) => $request;
        };
    }

    public function getResolvedMiddleware(): array
    {
        return $this->resolvedMiddleware;
    }

    public function registerGlobalMiddleware(array $middleware): void
    {
        foreach ($middleware as $m) {
            if (!$m instanceof Middleware) {
                throw new RouteException("Middleware {$m} is not an instance of Middleware.");
            }
            $this->globalMiddleware[] = $m;
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

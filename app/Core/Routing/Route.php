<?php

namespace App\Core\Routing;

use App\Core\Middleware;
use Closure;
use InvalidArgumentException;

class Route
{
    protected string $uri;
    protected string|Closure $controller;
    protected string|null $action;
    protected array $middleware = [];
    protected array $parameters = [];
    protected string $method;

    protected string $name;

    public function __construct($method, $uri)
    {
        $this->method = $method;
        $this->uri = $this->extractParameters($uri);
    }

    private function extractParameters($uri): string
    {

        preg_match_all('/\{([a-z]+)\}/', $uri, $matches);

        foreach ($matches[1] as $match) {
            $this->parameters[$match] = '([a-z0-9-]+)';
        }

        return $uri;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    // This method will check if the route matches the request
    public function matches(string $method, string $uri): bool
    {

        // we are adding the start and end delimiters to the regex pattern
        $pattern = '@^' . $this->uri . '$@';

        // we then loop through the parameters and replace the placeholders with the regex patterns
        foreach ($this->parameters as $key => $regex) {
            $pattern = str_replace('{' . $key . '}', $regex, $pattern);
        }

        // we then check if the method and URI match the pattern
        return $this->method === $method && preg_match($pattern, $uri);
    }

    public function getRequestParams(string $uri): array
    {
        // Build a regex pattern to match the request URI
        $pattern = '@^' . $this->uri . '$@';

        foreach ($this->parameters as $key => $regex) {
            $pattern = str_replace('{' . $key . '}', $regex, $pattern);
        }

        // Match the request URI against the pattern and capture the parameter values
        preg_match($pattern, $uri, $matches);

        // Remove the first element of $matches, which contains the full match
        array_shift($matches);

        // Combine the parameter names and values into an associative array
        return array_combine(array_keys($this->parameters), $matches);
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getParameter(mixed $index): string
    {
        return $this->parameters[$index] ?? '';
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function setMiddleware(array $middleware): void
    {
        foreach ($middleware as $m) {
            if (!is_string($m)) {
                throw new InvalidArgumentException('Middleware must be a string');
            }
            if (!app()->has($m)) {
                throw new InvalidArgumentException("Middleware {$m} is not registered");
            }
            if (!app()->resolve($m) instanceof Middleware) {
                throw new InvalidArgumentException("Middleware {$m} is not an instance of Middleware");
            }
            $this->middleware[] = $m;
        }
    }

    public function getAction(): string|null
    {
        return $this->action ?? null;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getController(): string|Closure
    {
        return $this->controller;
    }

    public function setController(string|array|Closure $controller): void
    {
        // if it's a string, we assume that it's a class name
        if (is_string($controller)) {
            $this->controller = $controller;
        }

        // if it's a closure, we assume that it's an anonymous function
        if ($controller instanceof Closure) {
            $this->controller = $controller;
        }

        // if an array, we need to split the class and method names
        if (is_array($controller)) {
            $this->controller = $controller[0];
            $this->setAction($controller[1]);
        }

    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function generate(array $params = []): string
    {
        return (new UrlResolver)->resolve($this, $params);
    }

}
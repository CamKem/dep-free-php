<?php

namespace App\Core\Http;

use App\Core\Routing\Route;
use App\Core\Routing\Router;

class Request
{
    protected string $method;
    protected string $uri;
    protected string $url;
    protected array $routeParameters = [];
    protected array $headers;
    protected array $bodyParameters;
    protected array $queryParameters;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $this->stripQueryString($_SERVER['REQUEST_URI']);
        $this->url = $_SERVER['REQUEST_URI'];
        $this->headers = getallheaders();
        $this->bodyParameters = $_POST;
        $this->queryParameters = $_GET;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getPath(): string
    {
        return parse_url($this->uri, PHP_URL_PATH);
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getParameters(): array
    {
        return array_merge($this->queryParameters, $this->bodyParameters, $this->routeParameters);
    }

    public function getBody(): array
    {
        return $this->bodyParameters;
    }

    // only method returns only the values of the keys passed in the array
    public function only(array $keys): array
    {
        return array_filter($this->getParameters(), static fn($key) =>
            in_array($key, $keys, true), ARRAY_FILTER_USE_KEY
        );
    }

    public function has(string $key): bool
    {
        return isset($this->getParameters()[$key]);
    }

    public function get(string $key, $default = null)
    {
        return $this->getParameters()[$key] ?? $default;
    }

    public function stripQueryString(string $uri): string
    {
        if (str_contains($uri, '?')) {
            return explode('?', $uri)[0];
        }

        return $uri;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function route(): Route
    {
        return app(Router::class)->getRoutes()->match($this);
    }

    public function setRouteParameters(): array
    {
        return $this->routeParameters = $this->route()->getRequestParams($this->uri);
    }

}
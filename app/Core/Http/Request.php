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
        $this->bodyParameters = $this->getJsonBody() ?? $_POST;
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

    // only method returns only the values of the keys passed in the array, if they exist
    // if the don't exist it should return an empty key
    // to return an empty key you can use the null coalescing operator
    // like this $this->getParameters()[$key] ?? null
    public function only(array $keys): array
    {
        $parameters = $this->getParameters();
        return array_merge(
            array_fill_keys($keys, null),
            array_intersect_key($parameters, array_flip($keys))
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

    public function all(): array
    {
        return $this->getParameters();
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

    private function getJsonBody(): ?array
    {
        if (!str_contains($this->headers['Content-Type'], 'application/json')) {
            return null;
        }
        $body = file_get_contents('php://input');
        return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    }

    public function isJson(): bool
    {
        return str_contains($this->headers['Content-Type'], 'application/json');
    }

}
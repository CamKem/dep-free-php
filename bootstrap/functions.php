<?php

use App\Core\App;
use App\Core\Authentication\Auth;
use App\Core\Caching\Cache;
use App\Core\Collecting\Collection;
use App\Core\Config;
use App\Core\Env;
use App\Core\FileSystem\Storage;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Routing\Router;
use App\Core\Session;
use App\Core\Template;

if (! function_exists('dd')) {
    function dd(...$values): void
    {
        foreach ($values as $value) {
            echo "<pre>";
            var_dump($value);
            echo "</pre>";
        }
        die();
    }
}

function urlIs($value): bool
{
    return request()->getUri() === $value;
}

/**
 * Generate a URL for the given route.
 *
 * @param string $name (The name of the route)
 * @param string|array|null $params (optional)
 * @return string
 */
function route(string $name, string|array|null $params = []): string
{
    if (! is_array($params)) {
        $params = [$params];
    }

    // If there's only one parameter & it's not an associative array
    // use the first key from the route parameters
    if (count($params) === 1 && array_keys($params)[0] === 0) {
        $routeParameters = app(Router::class)->getRoute($name)?->getParameters();
        $params = [key($routeParameters) => $params[0]];
    }

    return app(Router::class)->generate($name, $params ?? []);
}

if (! function_exists('sanitize')) {
    function sanitize(string $value): string
    {
        return htmlspecialchars(
            filter_var(
                trim($value)
                , FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            , ENT_QUOTES);
    }
}

function abort($code = 404): Template
{
    http_response_code($code);
    return view("errors.$code",
        ['title' => $code]
    );
}

function collect(array $items = []): Collection
{
    return new Collection($items);
}

function authorize($condition, $status = Response::FORBIDDEN): true
{
    if (! $condition) {
        abort($status);
    }

    return true;
}

function include_path($path): string
{
    return BASE_PATH . $path;
}

function class_basename($class): string
{
    $class = is_object($class) ? $class::class : $class;
    return basename(str_replace('\\', '/', $class));
}

function config($key): mixed
{
    return app(Config::class)->get($key);
}

function configure(callable $config): callable
{
    return app(Config::class)::storeConfigClosure($config);
}

function env($key, $default = null): string
{
    return app(Env::class)->get($key, $default);
}

function response(): Response
{
    /** @var Response $response */
    return app(Response::class);
}

function redirect($path = null): Response
{
    if ($path === null) {
        return response();
    }
    return response()->redirect($path);
}

function session(): Session
{
    return app(Session::class);
}

function request($key = null, $default = null): mixed
{
    if ($key === null) {
        return app(Request::class);
    }
    /** @var Request $request */
    return app(Request::class)->get($key, $default);
}

/**
 * Log a message to the errors log
 * Find the logs by using: echo ini_get('error_log');
 * @param string $message
 * @param string $level
 * @param array|null $context
 */
function logger(string $message, string $level = 'info', ?array $context = null): void
{
    error_log("[$level] $message: " . print_r($context, true));
}

function auth(): Auth
{
    return app(Auth::class);
}

/**
 * Resolve a class from the container, via the App class
 * @param string|null $key
 * @return object
 */
function app(?string $key = null): object
{
    if ($key === null) {
        return App::getContainer();
    }
    return App::getContainer()->resolve($key);
}

function view(string $path, array $data = []): Template
{
    return Template::make($path, $data);
}

function add(string $path, array $data = []): string
{
    return Template::make($path, $data, true)->render();
}

function csrf_token(): string
{
    return session()->get('_token');
}

function old($key, $default = ''): string
{
    return session()->old($key, $default);
}

function error($key): ?string
{
    return session()->error($key);
}

function cookie($key): ?string
{
    return $_COOKIE[$key] ?? null;
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

function storage(): Storage
{
    return app(Storage::class);
}

function cache(): mixed
{
    return app(Cache::class);
}
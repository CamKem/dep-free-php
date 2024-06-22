<?php

use App\Core\App;
use App\Core\Caching\Cache;
use App\Core\Exceptions\RouteException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Routing\Router;
use App\Services\AuthService;
use App\Services\CategoryService;
use App\Services\ConfigService;
use App\Services\DatabaseService;
use App\Services\EnvService;
use App\Services\MiddlewareService;
use App\Services\RouterService;
use App\Services\SessionService;
use App\Services\StorageService;

// Create the application & container.
$app = new App();

// Register service providers in the correct order
$app->registerProvider(new EnvService($app));
$app->registerProvider(new ConfigService($app));
$app->registerProvider(new DatabaseService($app));
$app->registerProvider(new SessionService($app));
$app->registerProvider(new AuthService($app));
$app->registerProvider(new CategoryService($app));
$app->registerProvider(new StorageService($app));
$app->registerProvider(new RouterService($app));

// Bind the Request & Response to the container
$app->singleton(Request::class);
$app->bind(Response::class, static fn() => new Response());

$app->registerProvider(new MiddlewareService($app));

// register the cache class
$app->bind(Cache::class, static fn() => Cache::create(config('cache.driver')));

// Boot the Application
$app->boot();

// Set the error handler
if(config('app.env') === 'local') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// set the exception handler
set_exception_handler(static function (Throwable $e) use ($app) {
    /** @var Request $request */
    $request = $app->resolve(Request::class);
    /** @var Response $response */
    $response = $app->resolve(Response::class);
    if ($e instanceof JsonException || $request->wantsJson()) {
        return $response->json([
            'error' => $e->getMessage()
        ]);
    }
    return $response->view('errors.exception', [
            'title' => 'Exception',
            'message' => $e->getMessage()
        ]);
});

// Route the request
try {
    // Get the request from the container, bound in the service
    $request = $app->resolve(Request::class);
    // Get the router from the container, bound in the service
    $router = $app->resolve(Router::class);
    // Route the request
    /** @var Router $router */
    $router->dispatch($request);
} catch (RouteException $e) {
    die($e->getMessage());
}
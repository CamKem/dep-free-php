<?php

use App\Core\App;
use App\Core\Exceptions\Handler;
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
$app->registerProvider(new EnvService);
$app->registerProvider(new ConfigService);
$app->registerProvider(new DatabaseService);
$app->registerProvider(new SessionService);
$app->registerProvider(new AuthService);
$app->registerProvider(new CategoryService);
$app->registerProvider(new StorageService);
$app->registerProvider(new RouterService);

// Bind the Request & Response to the container
$app->singleton(Request::class);
$app->bind(Response::class, static fn() => new Response());

$app->registerProvider(new MiddlewareService);

// TODO: register the cache class
//$app->bind(Cache::class, static fn() => Cache::create(config('cache.driver')));

// Boot the Application
$app->boot();

$app->debugInfo();

// Set the error handler
if (config('app.env') === 'local') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// set the exception handler
set_exception_handler(static function (Throwable $e) {
    (new Handler())->handle($e);
});

// Get the router from the container
$router = $app->resolve(Router::class);
// Route the request and dispatch the router
/** @var Router $router */
$router->dispatch(request: request());
// Note: a RouteNotFoundException will be thrown if no route is matched.
<?php

use App\Core\App;
use App\Core\Exceptions\RouteException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Routing\Router;
use App\Core\Session;
use App\Core\View;
use App\Middleware\AuthMiddleware;
use App\Services\ConfigService;
use App\Services\DatabaseService;
use App\Services\EnvService;
use App\Services\RouterService;

// Create the application & container.
$app = new App();

// Register service providers in the correct order
$app->registerProvider(new EnvService($app));
$app->registerProvider(new ConfigService($app));
$app->registerProvider(new DatabaseService($app));
$app->registerProvider(new RouterService($app));

// Bind the session, request, and response services to the container
$app->bind(Session::class, static fn() => new Session());
$app->singleton(Request::class);
$app->bind(Response::class, static fn() => new Response());

// Middleware aliases for the application
$app->alias('auth', AuthMiddleware::class);

// Boot the Application
$app->boot();

// set the exception handler
set_exception_handler(static function (Throwable $e) use ($app) {
    return $app->resolve(Response::class)
        ->view('error.exception', ['message' => $e->getMessage()]);
});

// Route the request
try {
    // Get the request from the container, bound in the service
    $request = $app->resolve(Request::class);
    // Get the router from the container, bound in the service
    $router = $app->resolve(Router::class);
    // Route the request
    $router->dispatch($request);
} catch (RouteException $e) {
    die($e->getMessage());
}
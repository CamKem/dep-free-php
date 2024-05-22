<?php

namespace App\Services;

use App\Core\Authentication\Auth;
use App\Core\ServiceProvider;

class AuthService extends ServiceProvider
{
    public function register(): void
    {
        // Register AUTH service IN THE CONTAINER
        $this->app->singleton(Auth::class);
    }

    public function boot(): void
    {
        // Resolve the auth so it loads the user from the session
        $this->app->resolve(Auth::class);
    }
}
<?php

namespace App\Services;

use App\Actions\HandleCsrfTokens;
use App\Core\ServiceProvider;
use App\Core\Session;
use Override;
use Random\RandomException;

class SessionService extends ServiceProvider
{

    #[Override]
    public function register(): void
    {
        $this->app->singleton(Session::class);
    }

    /**
     * @throws RandomException
     */
    #[Override]
    public function boot(): void
    {
        $session = $this->app->resolve(Session::class);
        // check if there is already a csrf token in the session
        if (!$session->has('csrf_token')) {
            // if not, generate a new one
            $session->set('csrf_token', (new HandleCsrfTokens())->generateToken());
        }

    }
}
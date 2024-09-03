<?php

namespace App\Services;

use App\Core\ServiceProvider;
use App\Core\Session;
use App\Http\Actions\CsrfTokens;
use Override;
use Random\RandomException;

class SessionService extends ServiceProvider
{

    #[Override]
    public function register(): void
    {
        $this->app->singleton(Session::class);
    }

    /** @throws RandomException */
    #[Override]
    public function boot(): void
    {
        $session = $this->app->resolve(Session::class);
        if (!$session->has('_token')) {
            $session->set('_token', (new CsrfTokens())->handle(generate: true));
        }
    }
}
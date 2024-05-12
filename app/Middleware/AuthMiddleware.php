<?php

namespace App\Middleware;

use App\Core\Middleware;
use Override;

class AuthMiddleware extends Middleware
{
    #[Override]
    public function handle(): void
    {
        if (!session()->has('user')) {
            abort(403);
        }
    }
}
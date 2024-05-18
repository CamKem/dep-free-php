<?php

namespace App\Middleware;

use App\Core\Http\Request;
use App\Core\Middleware;
use Closure;
use Override;

class AuthMiddleware extends Middleware
{
    #[Override]
    public function handle(Request $request, Closure $next): Closure
    {
        if (!auth()->check()) {
            abort(403);
        }

        return $next($request);
    }
}
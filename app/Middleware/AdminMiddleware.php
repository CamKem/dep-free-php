<?php

namespace App\Middleware;

use App\Core\Http\Request;
use App\Core\Middleware;
use Closure;
use Override;

class AdminMiddleware extends Middleware
{
    #[Override]
    public function handle(Request $request, Closure $next): Closure
    {
        if (!auth()->check() || !auth()->user()?->isAdmin()) {
            abort(403);
        }

        return $next($request);
    }
}
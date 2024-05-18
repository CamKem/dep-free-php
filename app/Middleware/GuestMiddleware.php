<?php

namespace App\Middleware;

use App\Core\Http\Request;
use App\Core\Middleware;
use Closure;
use Override;

class GuestMiddleware extends Middleware
{
    #[Override]
    public function handle(Request $request, Closure $next): Closure
    {
        if (auth()->check()) {
            redirect(route('dashboard.index'));
        }
        return $next($request);
    }
}
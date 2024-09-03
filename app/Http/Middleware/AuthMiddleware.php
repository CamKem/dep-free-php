<?php

namespace App\HTTP\Middleware;

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
            session()->flash('flash-message', 'You must be logged in to view this page.');
            redirect()->route('login.index');
        }

        return $next($request);
    }
}
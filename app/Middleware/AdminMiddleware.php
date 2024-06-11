<?php

namespace App\Middleware;

use App\Core\Http\Request;
use App\Core\Middleware;
use App\Core\Template;
use Closure;
use Override;

class AdminMiddleware extends Middleware
{
    #[Override]
    public function handle(Request $request, Closure $next): Closure
    {
        if (!auth()->user()->isAdmin()) {
            session()->flash('flash-message', 'You are not authorized to view this page.');
            redirect()->route('home');
        }

        Template::layout('layouts.admin');

        return $next($request);
    }
}
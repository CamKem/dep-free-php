<?php

namespace app\HTTP\Middleware;

use App\Core\Http\Request;
use App\Core\Middleware;
use Closure;
use Override;
use function session;


class PreviousUrlMiddleware extends Middleware
{
    #[Override]
    public function handle(Request $request, Closure $next): Closure
    {
        return $next($request);
    }

    /**
     * Store the previous URL in the session.
     * After the request has been handled.
     * @see MiddlewareService::boot
     * @uses self::after
     */
    public function after(Request $request): void
    {
        session()->set('previous.url', $request->url());
    }

}
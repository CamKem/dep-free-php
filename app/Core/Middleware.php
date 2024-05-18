<?php

namespace App\Core;

use App\Core\Http\Request;
use App\Core\Http\Response;
use Closure;

abstract class Middleware
{
    abstract public function handle(Request $request, Closure $next): Closure;

    public function terminate(Request $request, Response $response): void
    {
        // to be performed after the response is prepared for sending,
        // and before it is sent, unless the script is terminated first
    }

}
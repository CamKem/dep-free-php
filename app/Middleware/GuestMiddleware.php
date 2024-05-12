<?php

namespace App\Middleware;

use App\Core\Middleware;
use Override;

class GuestMiddleware extends Middleware
{
    #[Override]
    public function handle(): void
    {

        if (auth()->check()) {
            redirect(route('dashboard.index'));
        }

    }
}
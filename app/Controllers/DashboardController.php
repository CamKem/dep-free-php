<?php

namespace App\Controllers;

use App\Core\View;

class DashboardController
{
    public function __invoke(): View
    {
        return view('users.dashboard', [
            'title' => 'Dashboard',
            'user' => auth()->user(),
        ]);
    }

}
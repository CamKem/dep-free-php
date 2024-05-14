<?php

namespace App\Controllers;

use App\Core\Template;

class DashboardController
{
    public function __invoke(): Template
    {
        return view('users.dashboard', [
            'title' => 'Dashboard',
            'user' => auth()->user(),
        ]);
    }

}
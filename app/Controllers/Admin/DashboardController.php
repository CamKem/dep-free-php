<?php

namespace App\Controllers\Admin;

use App\Core\Template;

class DashboardController
{

    public function __invoke(): Template
    {
        return view('admin.dashboard', [
            'title' => 'Dashboard',
        ]);
    }

}
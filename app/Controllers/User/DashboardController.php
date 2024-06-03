<?php

namespace app\Controllers\User;

use App\Core\Template;

class DashboardController
{
    public function __invoke(): Template
    {
        $orders = auth()->user()?->orders()
            ->query()
            //->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('users.dashboard', [
            'title' => 'Dashboard',
            'user' => auth()->user(),
            'orders' => $orders,
        ]);
    }

}
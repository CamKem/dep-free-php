<?php

namespace app\Controllers\User;

use App\Core\Template;
use App\Models\Order;

class DashboardController
{
    public function __invoke(): Template
    {
        return view('users.dashboard', [
            'title' => 'Dashboard',
            'user' => auth()->user(),
            'orders' => (new Order())
                ->query()
                ->with('products')
                ->where('user_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate(6),
        ]);
    }

}
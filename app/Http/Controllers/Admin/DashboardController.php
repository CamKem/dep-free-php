<?php

namespace app\Http\Controllers\Admin;

use App\Core\Template;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController
{

    public function __invoke(): Template
    {
        return view('admin.dashboard', [
            'title' => 'Dashboard',
            'users' => (new User)->query()->get(),
            'orders' => (new Order)->query()->get(),
            'categories' => (new Category)->query()->get(),
            'products' => (new Product)->query()->get(),
        ]);
    }

}
<?php

namespace app\HTTP\Controllers;

use App\Core\Controller;
use App\Core\Template;
use App\Models\Product;

class HomeController extends Controller
{

    public function __invoke(): Template
    {
        return view("home", [
            'title' => 'Home',
            'products' => (new Product())
                ->query()
                ->where('featured', true)
                ->with('category')
                ->get()
        ]);
    }

}
<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\Product;

class HomeController extends Controller
{

    public function __invoke(): View
    {
        return view("home", [
            'title' => 'Home',
            'products' => Product::where('featured', 1)->with('category')->get(),
        ]);
    }

}
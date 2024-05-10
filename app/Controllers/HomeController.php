<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\Category;

class HomeController extends Controller
{

    public function __invoke(): View
    {
       // dd(session()->get('categories'));

        return view("home", [
            'title' => 'Home',
            'categories' => (new Category)->all()->get(),
        ]);
    }

}
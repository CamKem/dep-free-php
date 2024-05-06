<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view("home", [
            'title' => 'Home',
        ]);
    }

}
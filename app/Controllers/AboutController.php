<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;

class AboutController extends Controller
{

    public function __invoke(): View
    {
        return view("about", [
            'title' => 'About page',
            'heading' => 'About Us',
        ]);
    }

}
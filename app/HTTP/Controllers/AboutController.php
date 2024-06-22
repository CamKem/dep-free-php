<?php

namespace app\HTTP\Controllers;

use App\Core\Controller;
use App\Core\Template;

class AboutController extends Controller
{
    public function __invoke(): Template
    {
        return view("about", [
            'title' => 'About Us',
        ]);
    }
}
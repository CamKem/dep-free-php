<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;

class CategoriesController extends Controller
{

    public function __invoke(): View
    {
        return view("info", [
            'heading' => 'PHP InfoController',
        ]);
    }

}
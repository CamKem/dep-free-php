<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\View;
use App\Models\Category;

class CategoriesController extends Controller
{

    public function show(Request $request): View
    {
        $category = Category::where('slug', $request->get('category'))->first();
        return view("categories.show", [
            'title' => $category->name,
            'category' => $category,
            'products' => $category->products()->get(),
        ]);
    }

}
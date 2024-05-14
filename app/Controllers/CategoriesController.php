<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Template;
use App\Models\Category;

class CategoriesController extends Controller
{

    public function show(Request $request): Template
    {
        $category = (new Category())->where('slug', $request->get('category'))->first();

        if (!$category) {
            abort();
        }

        return view("categories.show", [
            'title' => $category->name,
            'category' => $category,
            'products' => $category->products()->get(),
        ]);
    }

}
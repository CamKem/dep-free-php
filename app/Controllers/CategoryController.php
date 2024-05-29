<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Template;
use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{

    public function show(Request $request): Template
    {
        $category = (new Category())
            ->query()
            ->where('slug', $request->get('category'))
            ->first();

        if (!$category) {
            abort();
        }

        return view("categories.show", [
            'title' => $category->name,
            'category' => $category,
            'products' => (new Product())
                ->query()
                ->where('category_id', $category->id)
                ->get()
        ]);
    }

}
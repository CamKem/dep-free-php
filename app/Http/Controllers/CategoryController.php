<?php

namespace App\Http\Controllers;

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
            ->with('products')
            ->first();

        if (!$category) {
            abort();
        }

        return view("categories.show", [
            'title' => $category->get('name'),
            'category' => $category,
            'products' => (new Product())
                ->query()
                ->where('category_id', $category->id)
                ->with('category')
                ->get()
        ]);
    }

}
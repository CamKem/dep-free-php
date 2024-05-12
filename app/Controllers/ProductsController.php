<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\View;
use App\Models\Product;

class ProductsController extends Controller
{

    public function index(Request $request): View
    {
        $query = Product::with('category');

        if ($request->has('search')){
            $query->where('name', 'like', "%{$request->get('search')}%");
        }

        return view("products.index", [
            'title' => 'Products',
            'products' => $query->get(),
        ]);
    }

    public function show(Request $request): View
    {
        return view("products.show", [
            'title' => 'Product',
            'product' => Product::with('category')
                ->where('slug', $request->get('slug'))
                ->get(),
        ]);
    }

}
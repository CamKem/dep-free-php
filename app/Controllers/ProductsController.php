<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\View;
use App\Models\Product;

class ProductsController extends Controller
{

    public function index(): View
    {
        return view("products.index", [
            'title' => 'Products',
            'products' => Product::all(),
        ]);
    }

    public function show(Request $request): View
    {
        return view("products.show", [
            'title' => 'Product',
            'product' => Product::where('slug', $request->get('slug'))->get(),
        ]);
    }

    public function search(Request $request): View
    {
        return view("products.index", [
            'title' => 'Search',
            'products' => Product::where('name', 'like', "%{$request->get('search')}%")->get(),
        ]);
    }

}
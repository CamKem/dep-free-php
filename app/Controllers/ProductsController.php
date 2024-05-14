<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Template;
use App\Models\Product;

class ProductsController extends Controller
{

    public function index(Request $request): Template
    {
        $query = (new Product())->with('category');

        if ($request->has('search')){
            $query->where('name', 'like', "%{$request->get('search')}%");
        }

        return view("products.index", [
            'title' => 'Products',
            'products' => $query->get(),
        ]);
    }

    public function show(Request $request): Template
    {
        $product = (new Product())->with('category')
            ->where('slug', $request->get('product'))
            ->first();

        if (! $product) {
            return abort();
        }

        return view("products.show", [
            'title' => 'Product',
            'product' =>$product,
        ]);
    }

}
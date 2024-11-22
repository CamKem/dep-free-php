<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Template;
use App\Models\Product;

class ProductController extends Controller
{

    public function index(Request $request): Template
    {
        $products = (new Product())
            ->query()
            ->with('category')
            ->orderBy('name')
            ->get();

        if ($request->has('search')) {
            $search = $request->get('search');

            $filteredProducts = $products
                ->each(fn($product) => $product->lev = levenshtein($product->name, $search))
                ->sortBy('lev');

            // TODO: write a better fuzzy search algorithm
            //  the smaller the min levenshtein distance, the closer the match
            //  so the less impact the threshold has & the more strict the match should be so it's included
            $minLev = $products->min('lev');
            $threshold = $minLev + (strlen($search) / 2);
            $products = $filteredProducts
                ->filter(fn($product) => $product->lev <= $threshold);
        }

        return view("products.index", [
            'title' => 'Products',
            'products' => $products,
        ]);
    }

    public function show(Request $request): Template
    {
        $product = (new Product())
            ->query()
            ->with('category')
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
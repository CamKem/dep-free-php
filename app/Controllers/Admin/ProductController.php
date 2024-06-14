<?php

namespace App\Controllers\Admin;

use App\Core\Template;
use App\Models\Category;
use App\Models\Product;

class ProductController
{

    public function index(Request $request): Template
    {
        $products = (new Product())->query()
            ->with('category')
            ->withCount('orders')
            ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $products->where('name', 'like', "%{$request->get('search')}%")
                ->orWhere('description', 'like', "%{$request->get('search')}%");
        }

        return view('admin.products.index', [
            'title' => 'products',
            'title' => 'Manage Products',
            'products' => $products->paginate(5),
            'categories' => (new Category())->query()->get(),
        ]);
    }
        ]);
    }

    public function show(): Template
    {
        return view('admin.products.show', [
            'title' => 'User',
        ]);
    }

    public function create(): Template
    {
        return view('admin.products.create', [
            'title' => 'Create User',
        ]);
    }

    public function store(): Template
    {
        return view('admin.products.store', [
            'title' => 'Store User',
        ]);
    }

    public function edit(): Template
    {
        return view('admin.products.edit', [
            'title' => 'Edit User',
        ]);
    }

    public function update(): Template
    {
        return view('admin.products.update', [
            'title' => 'Update User',
        ]);
    }

    public function destroy(): Template
    {
        return view('admin.products.destroy', [
            'title' => 'Destroy User',
        ]);
    }

}
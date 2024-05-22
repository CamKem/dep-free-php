<?php

namespace App\Controllers\Admin;

use App\Core\Template;

class ProductController
{

    public function index(): Template
    {
        return view('admin.products.index', [
            'title' => 'products',
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
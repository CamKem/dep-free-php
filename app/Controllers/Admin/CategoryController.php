<?php

namespace App\Controllers\Admin;

class CategoryController
{

    public function index()
    {
        return view('admin.categories.index');
    }

    public function create()
    {
        return 'Category Create';
    }

    public function store()
    {
        return 'Category Store';
    }

    public function edit($id)
    {
        return 'Category Edit ' . $id;
    }

    public function update($id)
    {
        return 'Category Update ' . $id;
    }

    public function destroy($id)
    {
        return 'Category Destroy ' . $id;
    }

}
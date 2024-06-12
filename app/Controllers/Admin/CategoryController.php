<?php

namespace App\Controllers\Admin;

use app\Core\Database\Slugger;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Models\Category;

class CategoryController
{

    public function index()
    {
        return view('admin.categories.index', [
            'title' => 'Manage Categories',
            'categories' => (new Category())->query()->paginate(),
        ]);
    }


    public function store(Request $request): Response
    {
        $slug = Slugger::uniqueSlug($request->get('name'), Category::class, 'slug');

        $created = (new Category())->query()->create([
            'name' => $request->get('name'),
            'slug' => $slug,
        ])->save();

        if (!$created) {
            session()->set('flash-message', 'Category was not created');
            return redirect()->back();
        }
        session()->set('flash-message', 'Category created successfully');
        return redirect()->route('admin.categories.index');
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
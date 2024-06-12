<?php

namespace App\Controllers\Admin;

use app\Core\Database\Slugger;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Core\Validator;
use App\Enums\CategoryStatus;
use App\Models\Category;

class CategoryController
{

    public function index(Request $request): Template
    {
        $categories = (new Category())
            ->query()
            ->with('products')
            ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $categories->where('name', 'like', "%{$request->get('search')}%");
        }

        return view('admin.categories.index', [
            'title' => 'Manage Categories',
            'categories' => $categories->paginate(8),
            'statuses' => CategoryStatus::toValues(),
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
            session()->flash('flash-message', 'Category was not created');
            return redirect()->back();
        }
        session()->flash('flash-message', 'Category created successfully');
        return redirect()->route('admin.categories.index');
    }

    public function update(Request $request): Response
    {
        $category = (new Category())->query()->find($request->get('id'));

        $validated = (new Validator())->validate($request->only(['name', 'status']), [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'status' => ['required', 'string']
        ]);

        $slug = Slugger::uniqueSlug($request->get('name'), Category::class, 'slug');

        $updated = $category->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'status' => $validated['status'],
        ])->save();

        if (!$updated) {
            session()->flash('flash-message', 'Category was not updated');
            return redirect()->back();
        }
        session()->flash('flash-message', 'Category updated successfully');
        return redirect()->route('admin.categories.index');
    }

    public function destroy(Request $request): Response
    {
        $category = (new Category())->query()
            ->find($request->get('id'));

        $deleted = $category
            ->delete()->save();

        if (!$deleted) {
            session()->flash('flash-message', 'Category was not deleted');
            return redirect()->back();
        }
        session()->flash('flash-message', 'Category deleted successfully');
        return redirect()->route('admin.categories.index');
    }

}
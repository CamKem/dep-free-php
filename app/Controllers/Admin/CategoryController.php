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
            ->withCount('products')
            ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $categories->where('categories.name', 'like', "%{$request->get('search')}%");
        }

        return view('admin.categories.index', [
            'title' => 'Manage Categories',
            'categories' => $categories->paginate(8),
            'statuses' => CategoryStatus::toValues(),
        ]);
    }

    public function store(Request $request): Response
    {
        $validated = (new Validator())->validate($request->only(['name', 'status']), [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'status' => ['required', 'string'],
        ]);

        if ($validated->hasErrors()) {
            session()->flash('open-create-modal', true);
            session()->flash('flash-message', 'Category was not created');
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validated->getErrors());
        }

        $slug = Slugger::uniqueSlug($request->get('name'), Category::class, 'slug');

        $created = (new Category())->query()->create([
            'name' => $validated->get('name'),
            'status' => $validated->get('status'),
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

        if ($validated->hasErrors()) {
            session()->flash('flash-message', 'Category was not updated');
            return redirect()->back()->withErrors($validated->getErrors());
        }

        $categoryValues = $category->get();

        if ($categoryValues->name !== $validated->get('name')) {
            $slug = Slugger::uniqueSlug($request->get('name'), Category::class, 'slug');
        }

        $updated = $category->update([
            'name' => $validated->get('name'),
            'slug' => $slug ?? $categoryValues->slug,
            'status' => $validated->get('status'),
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
            ->withCount('products')
            ->find($request->get('id'))
            ->first();

        // ensure that the category does not have any products
        if ($category && $category->products_count > 0) {
            session()->flash('flash-message', 'Category has products and cannot be deleted');
            return redirect()->back();
        }

        $deleted = $category
            ->query()
            ->delete()->save();

        if (!$deleted) {
            session()->flash('flash-message', 'Category was not deleted');
            return redirect()->back();
        }
        session()->flash('flash-message', 'Category deleted successfully');
        return redirect()->route('admin.categories.index');
    }

}
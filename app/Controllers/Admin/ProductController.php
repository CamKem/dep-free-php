<?php

namespace App\Controllers\Admin;

use App\Actions\HandleCsrfTokens;
use app\Core\Database\Slugger;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Core\Validator;
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
            'title' => 'Manage Products',
            'products' => $products->paginate(5),
            'categories' => (new Category())->query()->get(),
        ]);
    }

    public function imageUpload(Request $request): Response
    {
        $file = null;
        if ($request->hasFile('image')) {
            $file = storage()->put(
                'images/products/',
                $request->getFile('image')
            );
        }
        if (!$file) {
            return response()->json([
                'filePath' => null,
                'message' => 'Image upload failed'
            ]);
        }
        return response()->json([
            'filePath' => $file,
            'message' => 'Image uploaded successfully'
        ]);
    }

    public function store(Request $request): Response
    {
        // create a boolean value for the feature checkboxes
        $request->merge([
            'featured' => $request->has('featured') ? 1 : 0,
        ]);

        $validated = (new Validator())->validate($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'price' => ['required', 'number'],
            'category_id' => ['required', 'integer'],
            'description' => ['required', 'string'],
            'image' => ['required', 'string'],
            'featured' => ['boolean'],
        ]);

        if ($validated->hasErrors()) {
            session()->flash('open-create-modal', true);
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validated->getErrors());
        }

        // create a slug for the product
        $slug = Slugger::uniqueSlug($validated->name, 'product', 'slug');

        // TODO: wrap all the validated magic calls into a get() method
        $product = (new Product())->query()->create([
            'name' => $validated->name,
            'slug' => $slug,
            'price' => $validated->price,
            'category_id' => $validated->category_id,
            'description' => $validated->description,
            'image' => $validated->image,
            'featured' => $validated->featured,
        ])->save();

        if (!$product) {
            session()->flash('open-create-modal', true);
            session()->flash('flash-message', 'Product creation failed');
            return redirect()->back()
                ->withInput($request->all());
        }

        session()->flash('flash-message', 'Product created successfully');
        return redirect()->route('admin.products.index');
    }

    public function update(Request $request): Response
    {
        return view('admin.products.update', [
            'title' => 'Update User',
        // validate the csrf token
        (new HandleCsrfTokens)->validateToken($request->get('csrf_token'));

        // validate the request
        $validated = (new Validator())->validate($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'price' => ['required', 'number'],
            'category_id' => ['required', 'integer'],
            'description' => ['required', 'string'],
            'image' => ['required', 'string'],
            'featured' => ['boolean'],
        ]);
        // check if the request has errors
        if ($validated->hasErrors()) {
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validated->getErrors());
        }
        // get the product
        $product = (new Product())->query()
            ->find($request->get('id'))
            ->first();

    }

    public function destroy(Request $request): Response
    {
        $product = (new Product())->query()
            ->find($request->get('id'))
            ->first();

        if (!$product) {
            session()->flash('flash-message', 'Product not found');
            return redirect()->route('admin.products.index');
        }

        // first delete the image from the storage
        $removed = storage()->delete("images/products/{$product->image}");

        if (!$removed) {
            session()->flash('flash-message', 'Product image deletion failed');
            return redirect()->route('admin.products.index');
        }

        $deleted = $product->query()->delete()->save();

        if (!$deleted) {
            session()->flash('flash-message', 'Product deletion failed');
            return redirect()->route('admin.products.index');
        }

        session()->flash('flash-message', 'Product deleted successfully');
        return redirect()->route('admin.products.index');
    }

}
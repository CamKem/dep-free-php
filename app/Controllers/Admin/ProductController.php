<?php

namespace App\Controllers\Admin;

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
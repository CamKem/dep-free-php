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

        if ($request->has('category')) {
            $products->where('category_id', $request->get('category'));
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
        // TODO: eventually we need to work out a way to have only the old value go into the correct modal.
        //  presently it's being server render into both the edit & create modals.
        //  in future, we could extract the edit value from the DOM rather than have a server rendered modal
        //  ready to go for each it. This would allow us to just use 1 modal for edit & 1 for create.
        //  this would help eliminate the problem.
        // NOTE: one modal for both wouldn't be the best because of the different inputs & Http method
        //  We could leverage datasets to store the old values in the DOM & then extract them when needed
        $validated = (new Validator())->validate($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:products,name'],
            'price' => ['required', 'number'],
            'sale_price' => ['number'],
            'category_id' => ['required', 'integer'],
            'description' => ['required', 'string'],
            'image' => ['required', 'string'],
            'featured' => ['boolean'],
        ]);

        if ($validated->hasErrors()) {
            session()->flash('open-product-create-modal', true);
            session()->flash('flash-message', 'Please check the form for errors');
            return redirect()->route('admin.products.index')
                ->withInput($request->all())
                ->withErrors($validated->getErrors());
        }

        // create a slug for the product
        $slug = Slugger::uniqueSlug($validated->get('name'), Product::class, 'slug');

        $product = (new Product())->query()->create([
            'name' => $validated->get('name'),
            'slug' => $slug,
            'price' => number_format(
                $validated->get('price'),
                2,
                '.',
                ''
            ),
            'sale_price' => $validated->get('sale_price') === ''
                ? null
                : number_format(
                    $validated->get('sale_price'),
                    2,
                    '.',
                    ''
                ),
            'category_id' => $validated->get('category_id'),
            'description' => $validated->get('description'),
            'image' => $validated->get('image'),
            'featured' => $validated->get('featured', 0),
        ])->save();

        if (!$product) {
            session()->flash('open-product-create-modal', true);
            session()->flash('flash-message', 'Product creation failed, please try again');
            return redirect()->route('admin.products.index')
                ->withInput($request->all());
        }

        session()->flash('flash-message', 'Product created successfully');
        return redirect()->route('admin.products.index');
    }

    public function update(Request $request): Response
    {
        // validate the csrf token
        (new HandleCsrfTokens)->validateToken($request->get('csrf_token'));

        // validate the request
        $validated = (new Validator())->validate($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'price' => ['required', 'number'],
            'category_id' => ['required', 'integer'],
            'sale_price' => ['number'],
            'description' => ['required', 'string'],
            'image' => ['required', 'string'],
            'featured' => ['boolean'],
        ]);

        // check if the request has errors
        if ($validated->hasErrors()) {
            session()->flash(
                'open-product-edit-modal',
                $request->get('id')
            );
            session()->flash(
                'flash-message',
                'Please check the form for errors'
            );
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validated->getErrors());
        }

        // get the product
        $product = (new Product())->query()
            ->find($request->get('id'))
            ->first();

        // check that the image is different from the current image
        if ($product->image !== $validated->get('image')) {
            // delete the current image
            $removed = storage()->delete("images/products/{$product->image}");

            // check if the image was not removed
            if (!$removed) {
                session()->flash(
                    'open-product-edit-modal',
                    $request->get('id')
                );
                session()->flash(
                    'flash-message',
                    'Please check the form for errors'
                );
                return redirect()
                    ->route('admin.products.index')
                    ->withInput($request->all());
            }
        }

        $slug = $product->slug;
        // if the name has changed, get a new slug
        if ($product->name !== $validated->get('name')) {
            $slug = Slugger::uniqueSlug($validated->get('name'), Product::class, 'slug');
        }

        // update the product
        $updated = $product->query()->update([
            'name' => $validated->get('name'),
            'slug' => $slug,
            'category_id' => $validated->get('category_id'),
            'description' => $validated->get('description'),
            'price' => number_format(
                $validated->get('price'),
                2,
                '.',
                '',
            ),
            'sale_price' => $validated->get('sale_price') === ''
                ? null
                : number_format($validated->get(
                    'sale_price'),
                    2,
                    '.',
                '',
                ),
            'image' => $validated->get('image'),
            'featured' => $validated->get('featured', 0),
        ])->save();

        // check if the product was not updated
        if (!$updated) {
            session()->flash('flash-message', 'Product update failed, please try again');
            session()->flash('open-product-edit-modal', true);
            return redirect()->route('admin.products.index');
        }

        session()->flash('flash-message', 'Product updated successfully');
        return redirect()->route('admin.products.index');
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
            session()->flash('flash-message', 'Failed to delete product image');
            return redirect()->route('admin.products.index');
        }

        $deleted = $product->query()->delete()->save();

        if (!$deleted) {
            session()->flash('flash-message', 'Failed to delete product');
            return redirect()->route('admin.products.index');
        }

        session()->flash('flash-message', 'Product deleted successfully');
        return redirect()->route('admin.products.index');
    }

}
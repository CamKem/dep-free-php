<?php

namespace app\HTTP\Controllers\Shop;

use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Core\Validator;
use App\Models\Order;
use App\Models\Product;

class OrderController extends Controller
{

    public function show(Request $request): Template|Response
    {
        $order = (new Order())
            ->query()
            ->with('products')
            ->find($request->get('order'))
            ->get();

        if ($order->isEmpty()) {
            session()->flash('flash-message', 'Order not found');
            return redirect()->route('dashboard.index');
        }

        // load the category for each product
        foreach ($order->products as $product) {
            /* @var Product $product */
            $product->load('category');
        }

        return view('shop.order', [
            'title' => 'Order',
            'order' => $order,
            'shipping' => '10',
            'tax' => '.10',
        ]);
    }

    public function store(Request $request): Response
    {

        $validated = Validator::validate(
            $request->all(),
            [
                'first_name' => ['required', 'string'],
                'last_name' => ['required', 'string'],
                'address' => ['required', 'string'],
                'city' => ['required', 'string'],
                'state' => ['required', 'string'],
                'postcode' => ['required', 'number', 'min:4', 'max:4'],
                'contact_number' => ['required', 'number'],
                'card_name' => ['required', 'string'],
                'card_number' => ['required', 'number', 'min:16', 'max:16'],
                'expiry_date' => ['required'],
                'ccv' => ['required', 'number', 'min:3', 'max:3'],
            ]);

        if ($validated->failed()) {
            session()->flash('flash-message', 'Error: Order not created, please re-complete the form');
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validated->errors());
        }

        $ids = array_values(
            array_map(fn($item) => $item['product_id'], session()->get('cart'))
        );

        $products = (new Product())
            ->query()
            ->select(['id', 'price'])
            ->whereIn('id', $ids)
            ->get();

        $items = session()->get('cart');

        $total = 0;
        foreach ($products->toArray() as $product) {
            foreach ($items as $item) {
                if ($item['product_id'] == $product['id']) {
                    $total += $product['price'] * $item['quantity'];
                }
            }
        }

        // concatenate the address
        $address = "{$validated->get('address')}, {$validated->get('city')}, {$validated->get('state')}, {$validated->get('postcode')}";

        $new = (new Order())
            ->query()
            ->create([
                'status' => 'pending',
                'user_id' => auth()->user()->id,
                'first_name' => $validated->get('first_name'),
                'last_name' => $validated->get('last_name'),
                'address' => $address,
                'contact_number' => $validated->get('contact_number'),
                'card_name' => $validated->get('card_name'),
                'card_number' => $validated->get('card_number'),
                'expiry_date' => $validated->get('expiry_date'),
                'ccv' => $validated->get('ccv'),
                'purchase_date' => now(),
                'total' => $total,
            ])->save();

        if (!$new) {
            session()->flash('flash-message', 'Error: Order not created');
            return redirect()->back();
        }

        foreach ($products->toArray() as $product) {
            foreach ($items as &$item) {
                // do not use strict comparison here
                if ($item['product_id'] == $product['id']) {
                    $item['price'] = $product['price'];
                }
            }
        }
        unset($item);


        // find the order we just created
        $order = (new Order())
            ->query()
            ->where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->first();

        // attach the products to the order
        $order->products()->attach($items);

        // now empty the cart
        session()->remove('cart');

        // add the cart items to the order
        session()->flash('flash-message', 'Order created successfully');

        return redirect()->route('orders.show', ['order' => $order->id]);
    }

    public function destroy(Request $request): Response
    {
        // find the order & delete it,
        //products will be deleted to because of cascade
        $removed = (new Order())->query()->find($request->get('order'))
            ->delete()->save();

        // check order was deleted
        if (!$removed) {
            session()->flash('flash-message', 'Error: Order not deleted');
            return redirect()->back();
        }

        session()->flash('flash-message', 'Order deleted successfully');
        return redirect()->route('dashboard.index');
    }

}
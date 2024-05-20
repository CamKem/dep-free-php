<?php

namespace app\Controllers\User;

use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Models\Product;

class CartController extends Controller
{
    public function show(): Template
    {
        // get the unique product ids from the cart
        $items = array_filter(array_unique(
            array_map(static fn($item) => 
                $item['product_id'], session()->get('cart', [])
            )
        ));

        $products = (new Product)
            ->query()
            ->whereIn('id', array_values($items))
            ->with('category')
            ->get();

        // add in the quantity for each product from the cart
        $products->map(static function ($product) {
            $product->quantity = session()->get('cart')[$product->id]['quantity'];
            return $product;
        });

        return view('cart.show', [
            'title' => 'Shopping Cart',
            'cart' => $products,
        ]);
    }

    public function store(Request $request): Response
    {
        $cart = session()->get('cart', []);

        $item = [
            'product_id' => $request->get('product_id'),
            'quantity' => $request->get('quantity', 1),
        ];

        if ($item['quantity'] < 1 || !is_numeric($item['quantity'])) {
            session()->flash('flash-message', 'Error: Invalid quantity');
            return redirect()->back();
        }
        if (!$item['product_id'] || !is_numeric($item['product_id'])) {
            session()->flash('error', 'Error: Invalid product');
            return redirect()->back();
        }

        if (array_key_exists($item['product_id'], $cart)) {
            $cart[$item['product_id']]['quantity'] += $item['quantity'];
        } else {
            $cart[$item['product_id']] = $item;
        }
        session()->set('cart', $cart);
        session()->flash('flash-message', 'Product added to cart successfully');


        return redirect()->back();
    }

    public function destroy(Request $request): Response
    {
        // either the product_id or the 'all' key
        $key = $request->only(['product_id', 'all']);

        if ($key['product_id']) {
            $cart = session()->get('cart', []);
            unset($cart[$key['product_id']]);
            session()->set('cart', $cart);
            $message = 'Product removed from cart successfully';
        } elseif ($key['all'] !== null) {
            session()->remove('cart');
            $message = 'Cart cleared successfully';
        }

        if (isset($message)) {
            session()->flash('flash-message', $message);
        }

        return redirect()->back();
    }

}
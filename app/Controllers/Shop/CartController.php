<?php

namespace app\Controllers\Shop;

use app\Actions\RetrieveCartProducts;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Models\Product;

class CartController extends Controller
{
    public function show(): Template
    {
        return view('shop.cart', [
            'title' => 'Shopping Cart',
            'cart' => (new RetrieveCartProducts())->get(),
            'shipping' => 10.00,
            'taxRate' => .10,
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
            session()->flash('flash-message', 'Error: Invalid product');
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

    public function update(Request $request): Response
    {
        $productId = $request->get('product_id');
        $quantity = $request->get('quantity', 1);

        if ($quantity < 1 || !is_numeric($quantity)) {
            return response()->status(400)
                ->json(['message' => 'Invalid quantity']);
        }

        $cart = session()->get('cart', []);

        if (!array_key_exists($productId, $cart)) {
            return response()->status(404)
                ->json(['message' => 'Product not found in cart']);
        }

        $cart[$productId]['quantity'] = $quantity;
        session()->set('cart', $cart);

        return response()->json([
                'product_id' => (int)$productId,
                'quantity' => (int)$quantity,
                'message' => 'Product quantity updated'
            ]);
    }

    public function destroy(Request $request): Response
    {
        $key = $request->only(['product_id']);

        if ($key['product_id']) {
            $cart = session()->get('cart', []);
            unset($cart[$key['product_id']]);
            session()->set('cart', $cart);
            session()->flash('flash-message', 'Product removed from cart');
        }

        return redirect()->back();
    }

}
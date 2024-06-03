<?php

namespace app\Controllers\Shop;

use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Core\Validator;
use App\Models\Order;
use App\Models\Product;

class OrderController extends Controller
{

    public function show(Request $request): Template
    {
        return view('shop.order', [
            'title' => 'Order',
            'order' => (new Order())
                ->query()
                ->with('products')
                ->find($request->get('order'))
                ->get(),
        ]);
    }

    public function store(Request $request): Response
    {

        $validated = (new Validator())->validate($request->all(), [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'address' => ['required'],
            'city' => ['required'],
            'state' => ['required'],
            'postcode' => ['required'],
            'contact_number' => 'required',
            'card_name' => 'required',
            'card_number' => 'required',
            'expiry_date' => 'required',
            'ccv' => 'required',
        ]);

        // concatenate the address
        $address = $validated['address'] . ', ' . $validated['city'] . ', ' . $validated['state'] . ', ' . $validated['postcode'];

        $new = (new Order())
            ->query()
            ->create([
                'status' => 'pending',
                'user_id' => auth()->user()->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'address' => $address,
                'contact_number' => $validated['contact_number'],
                'card_name' => $validated['card_name'],
                'card_number' => $validated['card_number'],
                'expiry_date' => $validated['expiry_date'],
                'ccv' => $validated['ccv'],
                'purchase_date' => now(),
            ])->save();

        if ($new === false) {
            session()->flash('error', 'Error: Order not created');
            return redirect()->back();
        }

        // find the order we just created
        $order = (new Order())
            ->query()
            ->where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $ids = array_values(
            array_map(fn($item) => $item['product_id'], session()->get('cart'))
        );

        $products = (new Product())
            ->query()
            ->select('id', 'price')
            ->whereIn('id', $ids)
            ->get();

        $items = session()->get('cart');

        foreach ($products->toArray() as $product) {
            foreach ($items as &$item) {
                if ($item['product_id'] === $product['id']) {
                    $item['price'] = $product['price'];
                }
            }
        }
        unset($item);

        // attach the products to the order
        $order->products()->attach($items);

        // now empty the cart
        session()->remove('cart');

        // add the cart items to the order
        session()->flash('success', 'Order created successfully');

        return redirect()->route('orders.show', ['order' => $order->id]);
    }

    public function destroy(Order $order): void
    {
        $order
            ->query()
            ->delete();

        session()->flash('success', 'Order deleted successfully');

        redirect()->route('orders.index');
    }

}
<?php

namespace app\Controllers\Shop;

use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Models\Order;

class OrderController extends Controller
{

    public function show(Order $order): Template
    {
        return view('order.show', compact('order'));
    }

    public function store(Request $request): Response
    {
        dd($request->all());
        $new = (new Order())
            ->query()
            ->create([
                'status' => 'pending',
                'user_id' => auth()->user()->id,
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

        // add the cart items to the order
        session()->flash('success', 'Order created successfully');

        redirect()->route('orders.show', ['order' => $order->id]);
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
<?php

namespace App\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;

class OrderController
{

    public function index(Request $request): Template
    {
        $orders = (new Order())
            ->query()
            ->with('user')
            ->with('products')
            ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $orders->where('status', 'like', "%{$request->get('search')}%");
        }

        return view('admin.orders.index', [
            'title' => 'Manage Orders',
            'orders' => $orders->paginate(15),
        ]);
    }

    public function show(Request $request): Template|Response
    {
        $order = (new Order())
            ->query()
            ->with('user')
            ->with('products')
            ->find($request->get('id'))
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

        return view('admin.orders.show', [
            'title' => 'Display Order',
            'crumbs' => [
                'Manage Orders' => route('admin.orders.index'),
                'Display Order' => route('admin.orders.show', ['id' => $request->get('id')]),
            ],
            'shipping' => 10,
            'tax' => 0.10,
            'order' => $order,
            'statuses' => OrderStatus::toValues(),
        ]);
    }

    public function update(Request $request): Response
    {
        $updated = (new Order())->query()
            ->find($request->get('id'))
            ->update([
                'status' => $request->get('status'),
            ])
            ->save();

        if (!$updated) {
            session()->flash('flash-message', 'Order could not be updated.');
            return redirect()->route('admin.orders.index');
        }

        session()->flash('flash-message', 'Order has been updated.');
        return redirect()->route('admin.orders.show', ['id' => $request->get('id')]);
    }

    public function destroy(Request $request): Response
    {
        $deleted = (new Order())->query()
            ->find($request->get('id'))
            ->delete()
            ->save();

        if (!$deleted) {
            session()->flash('flash-message', 'Order could not be deleted.');
            return redirect()->route('admin.orders.index');
        }

        session()->flash('flash-message', 'Order has been deleted.');
        return redirect()->route('admin.orders.index');
    }

}
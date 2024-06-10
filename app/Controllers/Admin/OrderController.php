<?php

namespace App\Controllers\Admin;

class OrderController
{

    public function index()
    {
        return view('admin.orders.index');
    }

    public function show($id)
    {
        return view('admin.orders.show', ['id' => $id]);
    }

    public function edit($id)
    {
        return view('admin.orders.edit', ['id' => $id]);
    }

    public function update($id)
    {
        return redirect()->route('admin.orders.show', ['id' => $id]);
    }

    public function destroy($id)
    {
        return redirect()->route('admin.orders.index');
    }

}
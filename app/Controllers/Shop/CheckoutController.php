<?php

namespace app\Controllers\Shop;

use app\Actions\RetrieveCartProducts;
use App\Core\Template;

class CheckoutController
{

    public function __invoke(): Template
    {
        return view('shop.checkout', [
            'title' => 'Order Checkout',
            'cart' => (new RetrieveCartProducts())->get(),
            'shipping' => 10.00,
            'taxRate' => .10,
        ]);
    }

}
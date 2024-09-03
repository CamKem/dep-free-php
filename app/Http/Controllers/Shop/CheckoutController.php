<?php

namespace app\HTTP\Controllers\Shop;

use App\Core\Template;
use app\HTTP\Actions\RetrieveCartProducts;

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
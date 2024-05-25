<?php

namespace App\Controllers;

use App\Core\Template;

class CheckoutController
{

    public function show(): Template
    {
        return view('checkout', [
            'title' => 'Checkout',
        ]);
    }

}
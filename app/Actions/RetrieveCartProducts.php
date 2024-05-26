<?php

namespace app\Actions;

use App\Core\Collecting\ModelCollection;
use App\Models\Product;

class RetrieveCartProducts
{

    public function get(): ModelCollection
    {
        $items = array_map(static fn($item) => $item['product_id'],
            session()->get('cart', []
            ));

        $products = (new Product)
            ->query()
            ->whereIn('id', array_values($items))
            ->with('category')
            ->get();

        $products->map(static function ($product) {
            $product->quantity = session()->get('cart')[$product->id]['quantity'];
            return $product;
        });

        return $products;
    }

}
<?php

namespace App\Models;

use app\Core\Database\Model;
use app\Core\Database\Relations\BelongsTo;
use app\Core\Database\Relations\HasManyThrough;

class Product extends Model
{
    protected string $table = 'products';

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // add a reverse relationship to the orders table
    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(
            Order::class,
            OrderProduct::class,
            'product_id',
            'order_id',
        );
    }

}
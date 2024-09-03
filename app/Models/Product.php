<?php

namespace App\Models;

use App\Core\Database\Model;
use App\Core\Database\Relations\BelongsTo;
use App\Core\Database\Relations\HasManyThrough;

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
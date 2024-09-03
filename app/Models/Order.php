<?php

namespace App\Models;

use App\Core\Database\Model;
use App\Core\Database\Relations\BelongsTo;
use App\Core\Database\Relations\HasManyThrough;
use App\Core\Database\Relations\HasOne;

class Order extends Model
{
    protected string $table = 'orders';

    public function category(): HasOne
    {
        return $this->hasOne(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(
            Product::class,
            OrderProduct::class,
            'order_id',
            'product_id',
            true
        );
    }

}
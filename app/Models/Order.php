<?php

namespace App\Models;

use app\Core\Database\Model;
use app\Core\Database\Relations\BelongsTo;
use app\Core\Database\Relations\HasManyThrough;
use app\Core\Database\Relations\HasOne;

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
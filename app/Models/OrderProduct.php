<?php

namespace App\Models;

use app\Core\Database\Model;
use app\Core\Database\Relations\BelongsTo;

class OrderProduct extends Model
{
    protected string $table = 'order_product';

    // TODO: set up a relationship in the query builder
    //  that allows for these pivot tables to be used
    //  Or do we use HasManyThrough? Or do we use a
    //  different method?

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

}
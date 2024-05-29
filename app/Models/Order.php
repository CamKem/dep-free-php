<?php

namespace App\Models;

use app\Core\Database\Model;
use app\Core\Database\Relations\BelongsTo;
use app\Core\Database\Relations\HasManyThrough;

class Order extends Model
{
    protected string $table = 'orders';

    # attributes
    // id
    // user_id
    // status
    // first_name
    // last_name
    // address (need to concatenate the form inputs)
    // contact number
    // card name
    // card number
    // expiry date
    // cvv
    // card_name
    // purchase date

    public function category(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): HasManyThrough
    {
        return $this->hasManyThrough(
            Product::class,
            OrderProduct::class,
            'order_id',
            'product_id'
        );
    }

}
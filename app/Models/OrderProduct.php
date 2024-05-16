<?php

namespace App\Models;

use app\Core\Database\Model;
use app\Core\Database\Relations\BelongsTo;

class OrderProduct extends Model
{
    protected string $table = 'order_product';

    // TODO: set up a relationship in the query builder
    //  that allows for these pivot tables to be used
    public function category(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
<?php

namespace App\Models;

use app\Core\Database\Model;
use app\Core\Database\Relations\BelongsTo;

class Order extends Model
{
    protected string $table = 'orders';

    public function category(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
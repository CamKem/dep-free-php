<?php

namespace App\Models;

use App\Core\Database\Model;
use App\Core\Database\Relations\BelongsTo;

class OrderProduct extends Model
{
    protected string $table = 'order_product';
}
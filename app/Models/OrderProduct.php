<?php

namespace App\Models;

use app\Core\Database\Model;
use app\Core\Database\Relations\BelongsTo;

class OrderProduct extends Model
{
    protected string $table = 'order_product';
}
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

}
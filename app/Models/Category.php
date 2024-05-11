<?php

namespace App\Models;

use app\Core\Database\Model;
use app\Core\Database\Relations\HasMany;

class Category extends Model
{
    protected string $table = 'categories';

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

}
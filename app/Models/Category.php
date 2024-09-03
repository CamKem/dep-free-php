<?php

namespace App\Models;

use App\Core\Database\Model;
use App\Core\Database\Relations\HasMany;

class Category extends Model
{
    protected string $table = 'categories';

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

}
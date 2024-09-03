<?php

namespace App\Models;

use App\Core\Database\Model;
use App\Core\Database\Relations\HasMany;
use App\Core\Database\Relations\HasManyThrough;

class User extends Model
{
    protected string $table = 'users';

    public function roles(): HasManyThrough
    {
        return $this->hasManyThrough(
            Role::class,
            RoleUser::class,
            'user_id',
            'role_id',
            true
        );
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isAdmin(): bool
    {
        return $this->roles()->query()->where('name', '=', 'admin')->exists();
    }

}
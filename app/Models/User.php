<?php

namespace App\Models;

use app\Core\Database\Model;

class User extends Model
{
    protected string $table = 'users';

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

}
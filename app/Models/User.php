<?php

namespace App\Models;

use app\Core\Database\Model;
use app\Core\Database\Relations\HasManyThrough;

class User extends Model
{
    protected string $table = 'users';

    public function role(): HasManyThrough
    {
        return $this->hasManyThrough(
            Role::class,
            RoleUser::class,
            'user_id',
            'role_id'
        );
    }

}
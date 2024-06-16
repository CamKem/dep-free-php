<?php

namespace App\Models;

use app\Core\Database\Model;

class Role extends Model
{
    protected string $table = 'roles';

    public function users()
    {
        // TODO: change this to belongsToMany by adding the new inverse polymorphic many-to-many relationship
        //  at a later date, for the current project we will use hasManyThrough
        return $this->hasManyThrough(
            User::class,
            RoleUser::class,
            'role_id',
            'user_id',
            true
        );
    }

}
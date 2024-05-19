<?php

namespace App\Models;

use app\Core\Database\Model;
use app\Core\Database\Relations\BelongsTo;
use app\Core\Database\Relations\HasOne;

class RoleUser extends Model
{
    protected string $table = 'role_user';

    // TODO: set up a relationship in the query builder
    //  that allows for these pivot tables to be used
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): HasOne
    {
        return $this->hasOne(Role::class);
    }

}
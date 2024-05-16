<?php

namespace App\Models;

use app\Core\Database\Model;
use app\Core\Database\Relations\BelongsTo;

class RoleUser extends Model
{
    protected string $table = 'role_user';

    // TODO: set up a relationship in the query builder
    //  that allows for these pivot tables to be used
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // TODO: set up a HasOne relationship in the query builder
    public function role(): HasOne
    {
        return $this->belongsTo(Role::class);
    }

}
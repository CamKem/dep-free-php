<?php

namespace App\Models;

use app\Core\Database\Model;
use app\Core\Database\Relations\BelongsTo;

class Role extends Model
{
    protected string $table = 'roles';

}
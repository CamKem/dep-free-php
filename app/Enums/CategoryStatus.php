<?php

namespace App\Enums;

use App\Core\Traits\EnumMethods;

enum CategoryStatus: string
{
    use EnumMethods;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case REMOVED = 'removed';

}
<?php

namespace App\Enums;

enum CategoryStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case REMOVED = 'removed';

}
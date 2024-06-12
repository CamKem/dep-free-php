<?php

namespace App\Enums;

use App\Traits\EnumMethods;

enum OrderStatus: string
{
    use EnumMethods;

    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

}
<?php

namespace App\Core\Exceptions;

use App\Core\Exceptions\ExceptionInterface;
use Throwable;

class RouteException extends \InvalidArgumentException implements ExceptionInterface
{
    public function __construct($message = 'Route not found', $code = 0, Throwable|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
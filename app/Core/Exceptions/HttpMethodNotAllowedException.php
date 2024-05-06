<?php

namespace App\Core\Exceptions;

class HttpMethodNotAllowedException extends \InvalidArgumentException implements ExceptionInterface
{
    public function __construct($message = 'Route not found', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
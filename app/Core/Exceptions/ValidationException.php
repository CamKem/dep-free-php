<?php

namespace App\Core\Exceptions;

use Override;
use Throwable;

class ValidationException extends \InvalidArgumentException implements ExceptionInterface
{
    public function __construct($message, $code = 0, Throwable|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
<?php

namespace App\Core\Exceptions;

use SensitiveParameter;

class HttpNotFoundException extends \InvalidArgumentException implements ExceptionInterface
{
    public function __construct(
        string                      $message = '',
        \Throwable|null             $previous = null,
        int                         $code = 0,
        #[SensitiveParameter] array $headers = []
    )
    {
        parent::__construct(404, $message, $previous, $headers, $code);
    }

}
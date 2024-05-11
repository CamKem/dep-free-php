<?php

namespace App\Core\Exceptions;

use Override;
use Throwable;

class ValidationException extends \InvalidArgumentException implements ExceptionInterface
{
    public function __construct(array $errors, $code = 0, Throwable|null $previous = null)
    {
        $message = implode(', ', array_map(static function($field, $errors) {
            return $field . ': ' . implode(', ', $errors);
        }, array_keys($errors), $errors));
        parent::__construct($message, $code, $previous);
    }

}
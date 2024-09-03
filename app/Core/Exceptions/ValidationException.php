<?php

namespace App\Core\Exceptions;

use Exception;

class ValidationException extends Exception
{

    protected readonly array $errors;
    protected readonly array $old;

    /** @throws ValidationException */
    public static function throw($errors, $old): void
    {
        $instance = new static;

        $instance->errors = $errors;
        $instance->old = $old;

        throw $instance;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function old(): array
    {
        return $this->old;
    }

}
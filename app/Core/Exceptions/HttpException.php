<?php

namespace App\Core\Exceptions;

use App\Core\Exceptions\ExceptionInterface;
use SensitiveParameter;
use Throwable;

class HttpException extends \RuntimeException implements ExceptionInterface
{

    private int $statusCode;
    private array $headers;

    public function __construct(
        int                          $statusCode,
        string                       $message = '',
        Throwable|null               $previous = null,
        #[SensitiveParameter]array   $headers = [],
        int                          $code = 0
    )
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;

        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(#[SensitiveParameter] array $headers): void
    {
        $this->headers = $headers;
    }

}
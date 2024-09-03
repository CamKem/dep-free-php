<?php

namespace App\Http\Actions;

use Random\RandomException;
use SensitiveParameter;

class CsrfTokens
{

    /** @throws RandomException */
    public function handle(#[SensitiveParameter] ?string $token = null, ?bool $generate = null): string|bool
    {
        if ($generate) {
            return $this->generateToken();
        }

        if ($token) {
        return $this->validateToken($token);
        }

        return false;
    }

    /** @throws RandomException */
    public function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    public function validateToken(#[SensitiveParameter] string $token): bool
    {
        if (!hash_equals($token, session()->get(('_token')))) {
            return false;
        }
        return true;
    }

}
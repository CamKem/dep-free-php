<?php

namespace App\Actions;

use App\Core\Http\Request;
use Random\RandomException;
use RuntimeException;

class HandleCsrfTokens
{

    /**
     * Generate a random CSRF token
     *
     * @return string
     * @throws RandomException
     */
    public function randomCsrfToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Validate the CSRF token
     *
     * @param Request $request
     * @return true
     * @throws RuntimeException
     */
    public function validateToken(Request $request): true
    {
        if (!hash_equals(
            $request->getBody()['csrf_token'],
            session()->get('csrf_token')
        )) {
            throw new RuntimeException("CSRF token mismatch");
        }
        return true;
    }

}
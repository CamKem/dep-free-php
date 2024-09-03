<?php

namespace App\Mailing;

use App\Core\Mailer;
use SensitiveParameter;


class PasswordResetMail extends Mailer
{

    public function sendPasswordResetEmail(
        #[SensitiveParameter] string $email,
        #[SensitiveParameter] string $username,
        #[SensitiveParameter] string $token
    ): bool
    {
        return $this->send(
            to: $email,
            name: $username,
            subject: 'Password Reset',
            message: add('mails.password-reset', compact('token')),
        );
    }

}
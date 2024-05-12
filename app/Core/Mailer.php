<?php

namespace App\Core;

use SensitiveParameter;

class Mailer
{

    // TODO: implement an actual mailer and not log the email

    public function sendPasswordResetEmail(
        #[SensitiveParameter] string $email,
        #[SensitiveParameter] string $token
    ): void
    {
        logger("Sending password reset email to $email, token: $token");
        // log the reset link
        logger("link is: ".route('password.reset.edit', compact('token')));
    }

}
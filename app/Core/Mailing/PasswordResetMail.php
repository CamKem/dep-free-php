<?php

namespace app\Core\Mailing;

use SensitiveParameter;


class PasswordResetMail extends Mailer
{

    public function sendPasswordResetEmail(
        #[SensitiveParameter] string $email,
        #[SensitiveParameter] string $username,
        #[SensitiveParameter] string $token
    ): bool
    {
//        logger("Sending password reset email to {$email}, token: {$token}");
//        // log the reset link
//        logger("link is: " . route('password.reset.edit', compact('token')));
//
//        $message = "To reset your password, click on the following link: ";
//        $message .= "<a href='" . route('password.reset.edit', compact('token')) . "'>Reset Password</a>";

        return $this->send(
            to: $email,
            name: $username,
            subject: 'Password Reset',
            message: add('mails.password-reset', compact('token')),
        );
    }

}
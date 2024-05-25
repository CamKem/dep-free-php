<?php

namespace app\Mailing;

use App\Core\Mailer;
use SensitiveParameter;


class NewUserMail extends Mailer
{

    public function sendWelcomeMessage(
        #[SensitiveParameter] string $email,
        #[SensitiveParameter] string $username,
    ): bool
    {
        return $this->send(
            to: $email,
            name: $username,
            subject: 'Welcome to our platform',
            message: add('mails.welcome-message', compact('username')),
        );
    }

}
<?php

namespace App\Services;

use App\Core\Mailer;
use App\Models\PasswordReset;
use App\Models\User;
use RuntimeException;
use SensitiveParameter;

class PasswordResetService
{
    protected Mailer $mailer;

    public function __construct()
    {
        $this->mailer = new Mailer();
    }

    public function createPasswordReset(#[SensitiveParameter] string $email): bool
    {
        // Generate a unique token
        $token = bin2hex(random_bytes(20));

        // Save the token and email in the password_resets table
        $passwordReset = (new PasswordReset())->create([
            'email' => $email,
            'token' => $token,
        ]);
        $passwordReset->save();

        if (!$passwordReset) {
            throw new RuntimeException('Failed to create password reset token.');
        }

        // Send an email to the user with a link to reset their password
        $this->mailer->sendPasswordResetEmail($email, $token);

        return true;
    }

    public function resetPassword(
        #[SensitiveParameter] string $token,
        #[SensitiveParameter] string $password
    ): void
    {
        // Validate the token and get the associated email
        $passwordReset = (new PasswordReset())->where('token', $token)->first();
        if (!$passwordReset) {
            throw new RuntimeException('Invalid token.');
        }

        // Update the user's password in the users table
        $user = (new User())->where('email', $passwordReset->email)->update([
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);
        if (!$user) {
            throw new RuntimeException('User not found.');
        }

        $user->save();

        // Delete the token from the password_resets table
        $passwordReset->delete()->save();
    }
}
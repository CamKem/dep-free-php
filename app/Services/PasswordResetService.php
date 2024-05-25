<?php

namespace App\Services;

use app\Mailing\PasswordResetMail;
use App\Models\PasswordReset;
use App\Models\User;
use RuntimeException;
use SensitiveParameter;

class PasswordResetService
{
    private PasswordResetMail $mailer;

    public function __construct()
    {
        $this->mailer = new PasswordResetMail();
    }

    public function createPasswordReset(#[SensitiveParameter] string $email, string $username): bool
    {
        // Generate a unique token
        $token = bin2hex(random_bytes(20));

        // Save the token and email in the password_resets table
        $passwordReset = (new PasswordReset())
            ->query()
            ->create(compact('email', 'token'))
            ->save();

        if (!$passwordReset) {
            throw new RuntimeException('Failed to create password reset token.');
        }

        // Email the user with a link to reset their password
        $emailed = $this->mailer->sendPasswordResetEmail($email, $username, $token);

        if (!$emailed) {
            return false;
        }

        return true;
    }

    public function resetPassword(
        #[SensitiveParameter] string $token,
        #[SensitiveParameter] string $password
    ): bool
    {
        // Validate the token and get the associated email
        $passwordReset = (new PasswordReset())
            ->query()
            ->where('token', $token)
            ->first();
        if (!$passwordReset) {
            throw new RuntimeException('Invalid token.');
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Update the user's password in the users table
        $user = (new User())
            ->query()
            ->where('email', $passwordReset->email)
            ->update([
                'password' => $hashedPassword
            ])->save();

        // if the update returns false, throw an exception
        if (!$user) {
            throw new RuntimeException('Failed to reset password.');
        }

        // Delete the token from the password_resets table
        return $passwordReset->query()->delete()->save();
    }
}
<?php

namespace App\Core\Authentication;

use App\Models\User;
use SensitiveParameter;

class Auth
{

    protected ?User $user = null;

    public function __construct()
    {
        if (session()->has('user')) {
            $userExists = (new User())
                ->where('email', session()->get('user')->email)
                ->exists();
            if (!$userExists) {
                session()->remove('user');
            }
            $this->user = (new User())
                ->where('email', session()->get('user')->email)
                ->first();
        }
    }

    public function user(): ?User
    {
        return $this->user ?? session()->get('user');
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function login(User $user): void
    {
        $this->user = $user;
        session()->set('user', $user);
    }

    public function attempt(#[SensitiveParameter] array $credentials): bool
    {
        $user = $this->getUserByEmail($credentials['email']);
        if (!$user || !password_verify($credentials['password'], $user->password)) {
            return false;
        }

        $this->login($user);

        return true;
    }

    public function logout(): void
    {
        $this->user = null;
        session()->remove('user');
    }

    public static function getUserByEmail(#[SensitiveParameter] string $email): ?User
    {
        return (new User())
            ->where('email', $email)
            ->first();
    }

}
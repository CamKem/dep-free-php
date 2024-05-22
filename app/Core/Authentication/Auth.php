<?php

namespace App\Core\Authentication;

use App\Models\User;
use SensitiveParameter;

class Auth
{

    protected ?User $user = null;

    public function __construct()
    {
        $this->autoLogin();
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

        $remember = $credentials['remember'] ?? false;

        if ($remember) {
            $this->remember($user);
        }

        $this->login($user);

        return true;
    }

    public function logout(): void
    {
        $this->user = null;
        session()->remove('user');
        setcookie('remember', '', time() - 3600);
    }

    public function getUserByEmail(#[SensitiveParameter] string $email): ?User
    {
        return (new User())
            ->query()
            ->where('email', $email)
            // TODO add in after we fix the hasManyThrough relation
           // ->with('roles')->toRawSql());
           ->first();
    }

    public function remember(User $user): void
    {
        $token = bin2hex(random_bytes(32));
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);

        $user->query()->update(['remember_token' => $hashedToken])->save();

        $expiry = time() + 60 * 60 * 24 * 30;
        $cookieValue = $user->id . ':' . $token;

        setcookie('remember', $cookieValue, $expiry, '/', '', false, true);
    }

    public function autoLogin(): void
    {
        if (session()->has('user')) {
            $userExists = (new User())
                ->query()
                ->where('email', session()->get('user')->email)
                ->exists();
            if (!$userExists) {
                session()->remove('user');
            }
            $this->user = $this->getUserByEmail(session()->get('user')->email);
        } else if (cookie('remember')) {
            $this->loginWithCookie(cookie('remember'));
        }
    }

    public function loginWithCookie($cookie): void
    {
        $parts = explode(':', $cookie);
        if (count($parts) === 2) {
            [$userId, $token] = $parts;
            $user = (new User())->query()
                // TODO add in after we fix the hasManyThrough relation
                //->with('roles')
                ->find($userId)
                ->first();
            if (password_verify($token, $user->remember_token)) {
                $this->login($user);
            }
        } else {
            setcookie('remember', '', time() - 3600);
        }
    }

}
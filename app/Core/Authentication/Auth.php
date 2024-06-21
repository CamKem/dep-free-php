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
        // remove the user from the session
        session()->remove('user');
        // remove the remember me from the database
        (new User())->query()
            ->where('id', $this->user->id)
            ->update(['remember_token' => ''])
            ->save();
        // remove the remember me cookie
        setcookie('remember', '', time() - 3600);
        // remove the session cookie
        setcookie(session_name(), '', time() - 3600);
        // regenerate the session id
        session_regenerate_id(true);
        // finally, unset the user
        $this->user = null;
    }

    public function getUserByEmail(#[SensitiveParameter] string $email): ?User
    {
        return (new User())
            ->query()
            ->where('email', $email)
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
                ->find($userId)
                ->first();
            if ($user && password_verify($token, $user->remember_token)) {
                $this->login($user);
            } else {
                // destroy the cookie
                session()->flash('flash-message', 'Remember me cookie is invalid');
                setcookie('remember', '', time() - 3600);
            }
        } else {
            // destroy the cookie
            session()->flash('flash-message', 'Remember me cookie is invalid');
            setcookie('remember', '', time() - 3600);
        }
    }

}
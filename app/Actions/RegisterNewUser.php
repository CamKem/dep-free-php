<?php

namespace App\Actions;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Validator;
use app\Mailing\NewUserMail;
use App\Models\User;

class RegisterNewUser
{

    private NewUserMail $newUserMail;

    public function __construct()
    {
        $this->newUserMail = new NewUserMail();
    }

    public function register(Request $request): bool
    {
        $validated = $this->validate($request);
        $created = $this->createUser($validated);

        if (!$created) {
            return false;
        }
        $this->sendWelcomeEmail($validated);
        return true;
    }

    private function validate(Request $request): Response|array
    {
        $validated = (new Validator())->validate(
            $request->only(['username', 'email', 'password']), [
            'username' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $errors = [];

        $existingUser = (new User())
            ->query()
            ->where('email', $validated['email'])
            ->orWhere('username', $validated['username'])
            ->first();

        if ($existingUser) {
            if ($existingUser->email === $validated['email']) {
                $errors['email'] = 'Email already exists';
            }
            if ($existingUser->username === $validated['username']) {
                $errors['username'] = 'Username already exists';
            }

            return redirect(route('register.index'))
                ->withInput($validated)
                ->withErrors($errors);
        }

        return $validated;
    }

    private function createUser(array $validated): bool|Response
    {
        $user = (new User())
            ->query()
            ->create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => password_hash($validated['password'], PASSWORD_DEFAULT),
            ])->save();

        if (!$user) {
            return false;
        }

        return true;
    }

    private function sendWelcomeEmail(array $validated): void
    {
        $this->newUserMail->sendWelcomeMessage(
            email: $validated['email'],
            username: $validated['username']
        );
    }

}
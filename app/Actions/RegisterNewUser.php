<?php

namespace App\Actions;

use App\Core\Collecting\Collection;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Validator;
use app\Mailing\NewUserMail;
use App\Models\User;

class RegisterNewUser
{

    private Collection $validated;

    public function handle(Request $request): bool
    {
        $this->validate($request);

        $created = $this->createUser();
        if (!$created) {
            return false;
        }

        return $this->sendWelcomeEmail();
    }

    private function validate(Request $request): Response|Collection
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

        return $this->validated = collect($validated);
    }

    private function createUser(): bool|Response
    {
        $user = (new User())
            ->query()
            ->create([
                'username' => $this->validated->get('username'),
                'email' => $this->validated->get('email'),
                'password' => password_hash($this->validated->get('password'), PASSWORD_DEFAULT),
            ])->save();

        if (!$user) {
            return false;
        }

        return true;
    }

    private function sendWelcomeEmail(): bool
    {
        return (new newUserMail())->sendWelcomeMessage(
            email: $this->validated->get('email'),
            username: $this->validated->get('username')
        );
    }

}
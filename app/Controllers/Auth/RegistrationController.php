<?php

namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Core\Validator;
use App\Models\User;

class RegistrationController extends Controller
{

    public function index(): Template
    {
        return view('users.register', [
            'title' => 'Register',
        ]);
    }

    public function store(Request $request): Response
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

        $user = (new User())
            ->query()
            ->create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => password_hash($validated['password'], PASSWORD_DEFAULT)
            ])->save();

        if (!$user) {
            session()->flash('flash-message', 'There was an error creating your account. Please try again.');
            return redirect(route('register.index'))
                ->withInput($validated);
        }

        $user = (new User())
            ->query()
            ->where('email', $validated['email'])
            ->first();

        auth()->login($user);

        session()->flash('flash-message', 'You have successfully registered!');

        return redirect(route('dashboard.index'));
    }

}
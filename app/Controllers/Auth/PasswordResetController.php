<?php

namespace App\Controllers\Auth;

use App\Actions\HandleCsrfTokens;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Validator;
use App\Core\Template;
use App\Models\PasswordReset;
use App\Models\User;
use App\Services\PasswordResetService;

class PasswordResetController extends Controller
{

    public function show(): Template
    {
        return view('users.password-reset.show', [
            'title' => 'Reset Password'
        ]);
    }

    public function store(Request $request): Response
    {
        // validate the CSRF token
        (new HandleCsrfTokens())->validateToken($request->get('csrf_token'));

        // validate the email
        $validated = (new Validator())->validate($request->only(['email']), [
            'email' => ['required', 'email']
        ]);

        // check the email exists in the database
        $exists = (new User())->where('email', $validated['email'])->exists();

        if (!$exists) {
            return redirect(route('password.reset.index'))
                ->withInput($validated)
                ->withErrors(['email' => 'The provided email does not exist in our records.']);
        }

        // send the password reset email
        $sent = (new PasswordResetService())->createPasswordReset($validated['email']);

        if (!$sent) {
            return redirect(route('password.reset.index'))
                ->withInput($validated)
                ->withErrors(['email' => 'Failed to send the password reset email.']);
        }

        // flash a success message
        session()->flash('flash-message', 'An email has been sent with instructions to reset your password.');

        // redirect back to the password reset form
        return redirect(route('password.reset.show'));
    }

    public function edit(Request $request): Template|Response
    {
        // check the token exists in the database
        $exists = (new PasswordReset())->where('token', '=', $request->get('token'))->exists();

        // if the token does not exist, redirect back
        if (!$exists) {
            return redirect(route('password.reset.show'))
                ->withErrors(['email' => 'The provided token is invalid.']);
        }

        // if the token exists, show the password reset form
        return view('users.password-reset.edit', [
            'title' => 'Reset Password',
            'token' => $request->get('token')
        ]);
    }

    public function update(Request $request): Response
    {
        // validate the CSRF token
        (new HandleCsrfTokens())->validateToken($request->get('csrf_token'));

        // validate the password
        $validated = (new Validator())->validate($request->only(['password']), [
            'password' => ['required', 'min:8']
        ]);

        // reset the user's password
        (new PasswordResetService())->resetPassword($request->get('token'), $validated['password']);

        // flash a success message
        session()->flash('flash-message', 'Your password has been reset. Please log in.');

        // redirect to the login page
        return redirect(route('login.index'));
    }

}
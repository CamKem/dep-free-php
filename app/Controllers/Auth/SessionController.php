<?php

namespace App\Controllers\Auth;

use App\Actions\HandleCsrfTokens;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Core\Validator;

class SessionController extends Controller
{

    public function index(): Template
    {
        return view("users.login", [
            'title' => 'Login',
        ]);
    }

    public function store(Request $request): Response|Template
    {
        (new HandleCsrfTokens())->validateToken($request->get('csrf_token'));

        $validated = (new Validator())->validate(
            $request->only(['email', 'password']), [
            'email' => ['required', 'email', 'exists:user'],
            'password' => ['required'],
        ]);

        if ($validated->hasErrors()) {
            return redirect(route('login.index'))
                ->withInput($request->except(['password']))
                ->withErrors($validated->getErrors());
        }

        $login = auth()->attempt($validated->validatedData());

        if (!$login) {
            return redirect(route('login.index'))
                ->withInput($request->except(['password']))
                ->withErrors([
                    'email' => ['Your credentials are do not match.']
                ]);
        }

        session()->flash('flash-message', 'You have been logged in.');

        return redirect(route('dashboard.index'));

    }

    public function destroy(): Response
    {
        auth()->logout();
        session()->flash('flash-message', 'You have been logged out.');
        return redirect()->route('login.index');
    }

}
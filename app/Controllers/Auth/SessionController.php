<?php

namespace App\Controllers\Auth;

use App\Actions\HandleCsrfTokens;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Validator;
use App\Core\View;

class SessionController extends Controller
{

    public function index(): View
    {
        return view("users.login", [
            'title' => 'Login',
        ]);
    }

    public function store(Request $request): Response|View
    {
        (new HandleCsrfTokens())->validateToken($request->get('csrf_token'));

        $validated = (new Validator())->validate(
            $request->only(['email', 'password']), [
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $login = auth()->attempt($validated);

        if (!$login) {
            return redirect(route('login.index'))
                ->withInput($validated)
                ->withErrors(['email' => 'The provided credentials do not match our records.']);
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
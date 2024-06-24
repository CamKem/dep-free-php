<?php

namespace app\HTTP\Controllers\Auth;

use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Core\Validator;
use app\HTTP\Actions\CsrfTokens;

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
        (new CsrfTokens())->handle(token: $request->get('csrf_token'));

        $validated = Validator::validate(
                $request->only(['email', 'password', 'remember']), [
                'email' => ['required', 'email', 'exists:users'],
                'password' => ['required'],
                'remember' => ['boolean']
            ]);

        // TODO: WORK out if we want to explicitly check for failed validation here,
        //  or if we let the exception handler deal with it, by throwing a ValidationException
        //  in the Validator class.
        // NOTE: One think we need to consider, is that data is optional in the validate method.
        //  so we would need to consider ensuring that the correct data is returned in the response.
//        if ($validated->failed()) {
//            return redirect(route('login.index'))
//                ->withInput($request->except(['password']))
//                ->withErrors($validated->errors());
//        }

        $login = auth()->attempt($validated->data());

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
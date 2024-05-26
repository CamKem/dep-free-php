<?php

namespace App\Controllers\Auth;

use App\Actions\RegisterNewUser;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
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

        $registered = (new RegisterNewUser())->handle($request);

        if (!$registered) {
            session()->flash('flash-message', 'There was an error creating your account. Please try again.');
            return redirect(route('register.index'))
                ->withInput($request->all());
        }

        $user = (new User())
            ->query()
            ->where('email', $request->get('email'))
            ->first();

        auth()->login($user);

        session()->flash('flash-message', 'You have successfully registered!');
        return redirect(route('dashboard.index'));
    }

}
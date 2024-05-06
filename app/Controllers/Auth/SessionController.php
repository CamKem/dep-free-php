<?php

namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Core\Http\Response;
use App\Core\Validator;
use App\Core\View;

class SessionController extends Controller
{

    public function index(): View
    {
        return view("auth.login", [
            'heading' => 'Login',
            'errors' => []
        ]);
    }

    public function store(): Response|View
    {
        $errors = [];

        if (! Validator::string($_POST['email'], 1, 255)) {
            $errors['email'] = 'An email is required.';
        }

        if (! Validator::string($_POST['password'], 1, 255)) {
            $errors['password'] = 'A password is required.';
        }

        if (! empty($errors)) {
            return view("auth.login", [
                'heading' => 'Login',
                'errors' => $errors
            ]);
        }

        $user = $this->db->query('select * from users where email = :email', [
            'email' => $_POST['email']
        ])->first();

        if (! $user || ! password_verify($_POST['password'], $user->password)) {
            $errors['email'] = 'The provided credentials do not match our records.';
            return view("auth.login", [
                'heading' => 'Login',
                'errors' => $errors
            ]);
        }

        $_SESSION['user'] = $user->id;

        return redirect('/dashboard');

    }

    public function destroy(): Response
    {
        session()->remove('user');
        return redirect()->route('login.index');
    }

}
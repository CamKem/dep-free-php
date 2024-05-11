<?php

namespace App\Controllers;

use App\Actions\HandleCsrfTokens;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Validator;
use App\Core\View;

class ContactController extends Controller
{

    public function __construct()
    {
        if (!session()->has('csrf_token')) {
            session()->set('csrf_token', (new HandleCsrfTokens())
                ->randomCsrfToken());
        }
        parent::__construct();
    }


    public function index(): View
    {

        return view("contact", [
            'title' => 'Contact Us',
            'csrfToken' => session()->get('csrf_token')
        ]);
    }

    public function store(Request $request)
    {
        (new HandleCsrfTokens())->validateToken($request);

        // validate the request
        $validated = (new Validator)->validate($request->getBody(), [
            'first_name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['required', 'string', 'min:3', 'max:255'],
            'contact' => ['required', 'string', 'min:10', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:255'],
            'mailing_list' => ['boolean'],
        ]);
    }

}
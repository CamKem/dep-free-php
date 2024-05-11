<?php

namespace App\Controllers;

use App\Actions\HandleCsrfTokens;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Validator;
use App\Core\View;
use App\Models\Contact;

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

        $requestBody = $request->getBody();
        if (!isset($requestBody['mailing_list'])) {
            $requestBody['mailing_list'] = false;
        }

        $validated = (new Validator)->validate($requestBody, [
            'first_name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['required', 'string', 'min:3', 'max:255'],
            'contact' => ['required', 'string', 'min:10', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:255'],
            'mailing_list' => ['boolean'],
        ]);

        // store the $validated data in the database
        (new Contact)->store($validated);

        // store the request
        session()->set('flash-message', 'Your message has been sent successfully!');
        // redirect back with a success message
        return redirect(route('contact.index'));
    }

}
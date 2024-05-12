<?php

namespace App\Controllers;

use App\Actions\HandleCsrfTokens;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Validator;
use App\Core\View;
use App\Models\Contact;

class ContactController extends Controller
{

    public function index(): View
    {
        return view("contact", [
            'title' => 'Contact Us',
        ]);
    }

    public function store(Request $request): Response
    {
        // validate the csrf token
        (new HandleCsrfTokens())->validateToken($request->get('csrf_token'));

        // handle the checkbox
        $requestBody = $request->getBody();
        if (!isset($requestBody['mailing_list'])) {
            $requestBody['mailing_list'] = false;
        }

        // validate the request
        $validated = (new Validator)->validate($requestBody, [
            'first_name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['required', 'string', 'min:3', 'max:255'],
            'contact' => ['required', 'string', 'min:10', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:255'],
            'mailing_list' => ['boolean'],
        ]);

        // store the contact
        $contact = Contact::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'contact' => $validated['contact'],
            'email' => $validated['email'],
            'message' => $validated['message'],
            'mailing_list' => $validated['mailing_list'],
        ]);
        $contact->save();

        // store the request
        session()->set('flash-message', 'Your message has been sent successfully!');
        // redirect back with a success message
        return redirect(route('contact.index'));
    }

}
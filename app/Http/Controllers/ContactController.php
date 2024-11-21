<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Core\Validator;
use App\Http\Actions\CsrfTokens;
use App\Models\Contact;
use App\Services\MailChimpService;

class ContactController extends Controller
{

    public function index(): Template
    {
        return view("contact", [
            'title' => 'Contact Us',
        ]);
    }

    public function store(Request $request): Response
    {
        // validate the csrf token
        (new CsrfTokens())->handle(token: $request->get('csrf_token'));

        // validate the request
        $validated = Validator::validate(
            $request->only([
                'first_name', 'last_name', 'contact', 'email', 'message', 'mailing_list'
            ]),
            [
                'first_name' => ['required', 'string', 'min:3', 'max:255'],
                'last_name' => ['required', 'string', 'min:3', 'max:255'],
                'contact' => ['required', 'string', 'min:10', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'message' => ['required', 'string', 'min:10', 'max:255'],
                'mailing_list' => ['boolean'],
            ]
        );

        if ($validated->failed()) {
            session()->flash('flash-message', 'Please correct the form errors.');
            return response()->back()
                ->withInput($validated->data())
                ->withErrors($validated->errors());
        }

        // store the contact
        $contact = (new Contact())->query()->create([
            'first_name' => $validated->get('first_name'),
            'last_name' => $validated->get('last_name'),
            'contact' => $validated->get('contact'),
            'email' => $validated->get('email'),
            'message' => $validated->get('message'),
            'mailing_list' => $validated->get('mailing_list', false) === true ? 1 : 0,
        ]);
        $contact->save();

        if ($validated->get('mailing_list')) {
            $service = new MailChimpService('12805a4d3b');
            $response = $service->subscribe(
                email: $_POST['email'],
                mergeFields: [
                    'FNAME' => $_POST['first_name'] ?? '',
                    'LNAME' => $_POST['last_name'] ?? ''
                ]);
            if ($response->message() !== null) {
                session()->flash('flash-message', $response->message());
            } else {
                session()->flash('flash-message', 'An error occurred: ' . $response->message() ?? 'Unknown error');
            }
        }

        session()->flash('flash-message', 'Your message has been sent successfully!');
        return redirect(route('contact.index'));
    }

}
<?php

namespace app\HTTP\Controllers;

use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Core\Validator;
use app\HTTP\Actions\CsrfTokens;
use App\Models\Contact;

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
        $validated = (new Validator)->validate(
            $request->only(['first_name', 'last_name', 'contact', 'email', 'message', 'mailing_list']),
            [
                'first_name' => ['required', 'string', 'min:3', 'max:255'],
                'last_name' => ['required', 'string', 'min:3', 'max:255'],
                'contact' => ['required', 'string', 'min:10', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'message' => ['required', 'string', 'min:10', 'max:255'],
                'mailing_list' => ['boolean'],
            ]);

        if ($validated->hasErrors()) {
            session()->flash('flash-message', 'Please correct the form errors.');
            return response()->back()
                ->withInput($validated->data())
                ->withErrors($validated->getErrors());
        }

        // store the contact
        $contact = (new Contact())->query()->create([
            'first_name' => $validated->get('first_name'),
            'last_name' => $validated->get('last_name'),
            'contact' => $validated->get('contact'),
            'email' => $validated->get('email'),
            'message' => $validated->get('message'),
            'mailing_list' => $validated->get('mailing_list', false),
        ]);
        $contact->save();

        // store the request
        session()->flash('flash-message', 'Your message has been sent successfully!');
        // redirect back with a success message
        return redirect(route('contact.index'));
    }

}
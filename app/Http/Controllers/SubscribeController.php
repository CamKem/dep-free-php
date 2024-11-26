<?php

namespace App\Http\Controllers;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Core\Validator;
use App\Services\MailChimpService;

class SubscribeController
{

    /**
     * Display the subscription form
     */
    public function index(): Template
    {
        return view('subscribe', [
            'title' => 'Subscribe to our mailing list',
        ]);
    }

    /**
     * Store the subscription
     */
    public function store(Request $request): Response
    {
        // validate the request
        $validated = Validator::validate(
            $request->only(['email']),
            ['email' => ['required', 'email', 'max:255']]
        );

        if ($validated->failed()) {
            session()->flash('flash-message', 'Please correct the form errors.');
            return response()->back()
                ->withInput($validated->data())
                ->withErrors($validated->errors());
        }

        // subscribe the user to the mailing list
        $mailChimp = (new MailChimpService(env('MAILCHIMP_LIST_ID')))
            ->subscribe($validated->get('email'),
                [
                    'FNAME' => auth()->user()->first_name ?? '',
                    'LNAME' => auth()->user()->last_name ?? ''
                ]);

        if ($mailChimp->message() !== null) {
            return response()->back()
                ->with('flash-message', $mailChimp->message())
                ->with('action', 'error');
        }

        return response()->back()
            ->with('flash-message', 'You have been subscribed to our mailing list.')
            ->with('action', 'success');
    }

}
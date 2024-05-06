<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;

class ContactController extends Controller
{

    protected string $csrfToken;

    public function randomCsrfToken(): string
    {
        return $this->csrfToken = bin2hex(random_bytes(32));
    }


    public function index(): View
    {

        return view("contact", [
            'heading' => 'Contact Us',
            'csrfToken' => $this->randomCsrfToken()
        ]);
    }

    public function store()
    {
        // validate the request
    }

}
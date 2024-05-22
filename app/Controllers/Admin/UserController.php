<?php

namespace App\Controllers\Admin;

use App\Core\Template;

class UserController
{

    public function index(): Template
    {
        return view('admin.users.index', [
            'title' => 'Users',
        ]);
    }

    public function show(): Template
    {
        return view('admin.users.show', [
            'title' => 'User',
        ]);
    }

    public function create(): Template
    {
        return view('admin.users.create', [
            'title' => 'Create User',
        ]);
    }

    public function store(): Template
    {
        return view('admin.users.store', [
            'title' => 'Store User',
        ]);
    }

    public function edit(): Template
    {
        return view('admin.users.edit', [
            'title' => 'Edit User',
        ]);
    }

    public function update(): Template
    {
        return view('admin.users.update', [
            'title' => 'Update User',
        ]);
    }

    public function destroy(): Template
    {
        return view('admin.users.destroy', [
            'title' => 'Destroy User',
        ]);
    }

}
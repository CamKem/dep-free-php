<?php

namespace App\Controllers\Admin;

use App\Actions\RegisterNewUser;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Models\User;

class UserController
{

    public function index(Request $request): Template
    {
        $users = (new User())
            ->query()
            ->select('id', 'username', 'email', 'created_at')
            ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $users->where('username', 'like', "%{$request->get('search')}%");
            $users->orWhere('email', 'like', "%{$request->get('search')}%");
        }

        return view('admin.users.index', [
            'title' => 'Users',
            'users' => $users->paginate(8),
        ]);
    }

    public function show(Request $request): Template
    {
        return view('admin.users.show', [
            'title' => 'Display User',
            'user' => (new User())->query()->find($request->get('id')),
        ]);
    }

    public function edit(): Template
    {
        return view('admin.users.edit', [
            'title' => 'Edit User',
        ]);
    }

    public function update(): Response
    {
        // TODO: save the changes
    }

    public function store(Request $request): Response
    {
        $registered = (new RegisterNewUser())->handle($request);
        if (!$registered) {
            session()->flash('flash-message', 'There was an error registering the user. Please try again.');
            return redirect()->back()
                ->withInput($request->all());
        }

        $user = (new User())
            ->query()
            ->where('email', $request->get('email'))
            ->first();

        if (!$user) {
            session()->flash('flash-message', 'Error: Something went wrong.');
            return response()->back();
        }

        session()->flash('flash-message', 'You have successfully registered!');
        return redirect(route('admin.users.index'));
    }

    public function destroy(Request $request): Response
    {
        $user = (new User())->query()
            ->find($request->get('id'))
            ->delete()
            ->save();

        if (!$user) {
            session()->flash('flash-message', 'Error: User not found.');
            return response()->back();
        }

        session()->flash('flash-message', 'User deleted successfully.');
        return response()->redirect(route('admin.users.index'));
    }

}
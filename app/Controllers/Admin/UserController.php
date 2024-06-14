<?php

namespace App\Controllers\Admin;

use App\Actions\RegisterNewUser;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Core\Validator;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;

class UserController
{

    public function index(Request $request): Template
    {
        $users = (new User())
            ->query()
            ->select(['id', 'username', 'email', 'created_at'])
            ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $users->where('username', 'like', "%{$request->get('search')}%");
            $users->orWhere('email', 'like', "%{$request->get('search')}%");
        }

        return view('admin.users.index', [
            'title' => 'Manage Users',
            'users' => $users->paginate(8),
        ]);
    }

    public function show(Request $request): Template
    {
        return view('admin.users.show', [
            'title' => 'Display User',
            'crumbs' => [
                'Manage Users' => route('admin.users.index'),
                'Display User' => route('admin.users.show', ['id' => $request->get('id')]),
            ],
            'user' => (new User())
                ->query()
                ->with('roles')
                ->find($request->get('id'))
                ->get(),
            'orders' => (new Order())
                ->query()
                ->where('user_id', $request->get('id'))
                ->paginate(6),
            'roles' => (new Role())
                ->query()
                ->select(['id', 'name'])
                ->get(),
        ]);
    }

    public function update(Request $request): Response
    {
        $user = (new User())
            ->query()
            ->find($request->get('id'))
            ->first();

        if (!$user) {
            session()->flash('flash-message', 'Error: User not found.');
            return response()->back();
        }

        if ($request->has('remove_role') === true) {
            $user->roles()->detach(['role_id' => $request->get('role_id')]);
            session()->flash('flash-message', 'Role removed successfully.');
            return response()->redirect(route('admin.users.show', ['id' => $request->get('id')]));
        }

        // validate and update the users details
        $validated = (new Validator())->validate(
            $request->only(['username', 'email', 'roles']),
            [
                'username' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'roles' => ['array'],
            ]
        );

        if ($validated->hasErrors()) {
            session()->flash('flash-message', 'Error: Please check the form for errors.');
            return response()->back()
                ->withErrors($validated->getErrors())
                ->withInput($request->all());
        }

        $user->query()->update([
            'username' => $validated->get('username'),
            'email' => $validated->get('email'),
        ])->save();

        // if request has password hash it and then update the user
        if ($request->has('password')) {
            $user->query()->update([
                'password' => password_hash($request->get('password'), PASSWORD_DEFAULT),
            ])->save();
        }

        // get the user again
        $user = (new User())
            ->query()
            ->find($request->get('id'))
            ->first();

        // make an array with the roles using the role id as the key
        $roles = [];
        foreach ($validated->get('roles') as $role) {
            $roles[$role] = ['role_id' => $role];
        }

        // sync the roles
        $user->roles()->sync($roles);

        session()->flash('flash-message', 'User updated successfully.');
        return response()->redirect(route('admin.users.show', ['id' => $request->get('id')]));
    }

    public function store(Request $request): Response
    {
        $validated = (new Validator())->validate(
            $request->only(['username', 'email', 'password']), [
            'username' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if ($validated->hasErrors()) {
            return redirect(route('admin.users.index'))
                ->withInput($request->all())
                ->withErrors($validated->getErrors());
        }

        $errors = [];

        $existingUser = (new User())
            ->query()
            ->where('email', $validated->get('email'))
            ->orWhere('username', $validated->get('username'))
            ->exists();

        if (!$existingUser) {
            $user = (new User())
                ->query()
                ->where('email', $validated->get('email'))
                ->orWhere('username', $validated->get('username'))
                ->first();
            if ($user->email === $validated->get('email')) {
                $errors['email'] = 'Email already exists';
            }

            if ($user->username === $validated->get('username')) {
                $errors['username'] = 'Username already exists';
            }

            session()->flash('flash-message', 'Error: The user already exists.');
            return redirect(route('admin.users.index'))
                ->withInput($validated->validatedData())
                ->withErrors($errors);
        }

        $user = (new User())
            ->query()
            ->create([
                'username' => $validated->get('username'),
                'email' => $validated->get('email'),
                'password' => password_hash($validated->get('password'), PASSWORD_DEFAULT),
            ])->save();

        if (!$user) {
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
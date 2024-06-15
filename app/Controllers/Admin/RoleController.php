<?php

namespace App\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Core\Validator;
use App\Models\Role;

class RoleController
{

    public function index(Request $request): Template
    {
        $roles = (new Role())->query()
            ->withCount('users');

        if ($request->has('search')) {
            $roles->where('name', 'like', "%{$request->get('search')}%");
            $roles->orWhere('description', 'like', "%{$request->get('search')}%");
        }

        return view('admin.roles.index', [
            'title' => 'Manage Roles',
            'roles' => $roles->paginate(8),
        ]);
    }

    public function store(Request $request): Response
    {
        $validated = (new Validator())->validate($request->only(['name', 'description']), [
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        if ($validated->hasErrors()) {
            session()->flash('flash-message', 'Please check the form for errors');
            return response()->back()
                ->withInput($request->all())
                ->withErrors($validated->getErrors());
        }

        $role = (new Role())->query()
            ->create([
                'name' => $validated->get('name'),
                'description' => $validated->get('description'),
            ])->save();

        if (!$role) {
            session()->flash('flash-message', 'An error occurred while creating the role');
            return response()->back()
                ->withInput($request->all())
                ->withErrors(['An error occurred while creating the role']);
        }

        session()->flash('flash-message', 'Role created successfully');
        return response()->redirect(route('admin.roles.index'));
    }

    public function update(Request $request): Response
    {
        $validated = (new Validator())->validate($request->only(['name', 'description']), [
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        if ($validated->hasErrors()) {
            session()->flash('flash-message', 'Please check the form for errors');
            return response()->back()
                ->withInput($request->all())
                ->withErrors($validated->getErrors());
        }

        $role = (new Role())
            ->query()
            ->find($request->get('id'))
            ->update([
                'name' => $validated->get('name'),
                'description' => $validated->get('description'),
            ])->save();

        if (!$role) {
            session()->flash('flash-message', 'An error occurred while updating the role');
            return response()->back()
                ->withInput($request->all())
                ->withErrors(['An error occurred while creating the role']);
        }

        session()->flash('flash-message', 'Role updated successfully');
        return response()->redirect(route('admin.roles.index'));
    }

    public function destroy(Request $request): Response
    {
        // check that no users have the current role otherwise we can't delete it, redirect back with errors;
        $role = (new Role())
            ->query()
            ->withCount('users')
            ->where('id', $request->get('id'))
            ->first();

        if ($role && $role->users_count > 0) {
            session()->flash('flash-message', 'Cannot delete a role that has users assigned to it');
            return response()->back();
        }

        $deleted = $role->query()->delete()->save();

        if (!$deleted) {
            session()->flash('flash-message', 'An error occurred while deleting the role');
            return response()->back();
        }

        session()->flash('flash-message', 'Role deleted successfully');
        return response()->redirect(route('admin.roles.index'));
    }

}
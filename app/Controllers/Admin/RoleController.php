<?php

namespace App\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Models\Role;

class RoleController
{

    public function index(): Template
    {
        return view('admin.roles.index', [
            'title' => 'Manage Roles',
            'roles' => (new Role())->query()
                ->withCount('users')
                ->get()
        ]);
    }

    public function store(Request $request): Response
    {
        dd($request->all());
        return response()->json(['message' => 'Role Store']);
    }

    public function update(Request $request): Response
    {
        dd($request->all());
        return response()->json(['message' => 'Role Update']);
    }

    public function destroy(Request $request): Response
    {
        dd($request->all());
        return response()->json(['message' => 'Role Destroy']);
    }

}
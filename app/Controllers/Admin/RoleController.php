<?php

namespace App\Controllers\Admin;

class RoleController
{

    public function index()
    {
        return 'Role Index';
    }

    public function create()
    {
        return 'Role Create';
    }

    public function store()
    {
        return 'Role Store';
    }

    public function edit($id)
    {
        return 'Role Edit ' . $id;
    }

    public function update($id)
    {
        return 'Role Update ' . $id;
    }

    public function destroy($id)
    {
        return 'Role Destroy ' . $id;
    }

}
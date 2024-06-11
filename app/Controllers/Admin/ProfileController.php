<?php

namespace App\Controllers\Admin;

class ProfileController
{

    public function show($id)
    {
        return 'Profile Show ' . $id;
    }

    public function edit($id): string
    {
        return 'Profile Edit ' . $id;
    }

    public function update($id)
    {
        return 'Profile Update ' . $id;
    }

}
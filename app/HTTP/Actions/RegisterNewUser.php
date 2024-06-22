<?php

namespace app\HTTP\Actions;

use App\Core\Collecting\Collection;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Validator;
use app\Mailing\NewUserMail;
use App\Models\Role;
use App\Models\User;

class RegisterNewUser
{

    private Collection $validated;

    public function handle(Request $request): bool
    {
        $this->validated = $this->validate($request);

        $created = $this->createUser();
        if (!$created) {
            return false;
        }

        $this->addUserRole();

        $sent = $this->sendWelcomeEmail();

        if (!$sent) {
            $this->deleteUser();
            return false;
        }

        return true;
    }

    private function validate(Request $request): Response|Collection
    {
        $validated = (new Validator())->validate(
            $request->only(['username', 'email', 'password']), [
            'username' => ['required', 'min:3', 'max:20', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required']
        ]);

        if ($validated->hasErrors()) {
            return redirect()
                ->route('register.index')
                ->withInput($request->all())
                ->withErrors($validated->getErrors());
        }

        return collect($validated->data());
    }

    private function createUser(): bool
    {
        $user = (new User())
            ->query()
            ->create([
                'username' => $this->validated->get('username'),
                'email' => $this->validated->get('email'),
                'password' => password_hash($this->validated->get('password'), PASSWORD_DEFAULT),
            ])->save();

        if (!$user) {
            return false;
        }

        return true;
    }

    private function addUserRole(): void
    {
        $user = (new User())
            ->query()
            ->where('email', $this->validated->get('email'))
            ->first();

        if ($user) {
            // find the role with the name of 'user' and attach it to the user
            $role = (new Role())
                ->query()
                ->select('id')
                ->where('name', 'user')
                ->first();

            if ($role) {
                $id = $role->toArray();
                $id['role_id'] = $id['id'];
                unset($id['id']);
                // we need to wrap the id in an array
                // because the attach method expects the related_id
                // to be nested in an array
                $user->roles()->attach([$id]);
            }
        }

    }

    private function sendWelcomeEmail(): bool
    {
        return (new newUserMail())->sendWelcomeMessage(
            email: $this->validated->get('email'),
            username: $this->validated->get('username')
        );
    }

    private function deleteUser(): void
    {
        (new User())
            ->query()
            ->where('email', $this->validated->get('email'))
            ->delete()
            ->save();
    }

}
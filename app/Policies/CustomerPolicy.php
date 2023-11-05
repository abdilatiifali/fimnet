<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    public function view()
    {
        return true;
    }

    public function create()
    {
        return true;
    }

    public function update()
    {
        return true;
    }

    public function delete(User $user)
    {
        return $user->isAdmin();
    }
    public function attachAnyMonth(User $user)
    {
        return $user->isAdmin();
    }

    public function detachMonth(User $user)
    {
        return $user->isAdmin();
    }

}

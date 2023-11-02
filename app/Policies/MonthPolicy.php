<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MonthPolicy
{
    use HandlesAuthorization;

    public function view()
    {
        return true;
    }

    public function create()
    {
        return auth()->user()->isAdmin();
    }

    public function attachAnyCustomer(User $user)
    {
        return $user->isAdmin();
    }

    public function detachCustomer(User $user)
    {
        return $user->isAdmin();
    }
}

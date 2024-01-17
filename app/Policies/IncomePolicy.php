<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IncomePolicy
{
    use HandlesAuthorization;

    public function view()
    {
        return true;
    }

    public function viewAny()
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user)
    {
        return $user->isSuperAdmin();
    }

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
}

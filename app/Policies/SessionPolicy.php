<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SessionPolicy
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
}

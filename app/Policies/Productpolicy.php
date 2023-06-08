<?php

namespace App\Policies;

use App\Models\User;

class Productpolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return auth()->user()->role === 'admin';
    }
}

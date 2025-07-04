<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class NewFeaturesPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isDev();
    }
}

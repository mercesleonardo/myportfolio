<?php

namespace App\Policies;

use App\Models\{Profile, User};

class ProfilePolicy
{
    public function view(User $user, Profile $profile): bool
    {
        return $user->isAdmin() || (int) $user->id === (int) $profile->user_id;
    }

    public function update(User $user, Profile $profile): bool
    {
        return $user->isAdmin() || (int) $user->id === (int) $profile->user_id;
    }
}

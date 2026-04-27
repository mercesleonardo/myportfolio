<?php

namespace App\Actions\Admin\Users;

use App\Models\User;

final class RestoreUser
{
    public function handle(User $user): void
    {
        $user->restore();
    }
}

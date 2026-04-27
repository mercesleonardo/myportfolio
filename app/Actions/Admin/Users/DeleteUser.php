<?php

namespace App\Actions\Admin\Users;

use App\Models\User;

final class DeleteUser
{
    public function handle(User $user): void
    {
        $user->delete();
    }
}

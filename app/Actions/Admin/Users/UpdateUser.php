<?php

namespace App\Actions\Admin\Users;

use App\Models\User;
use Illuminate\Support\Arr;

final class UpdateUser
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(User $user, array $data): User
    {
        $data = Arr::only($data, [
            'name',
            'slug',
            'email',
            'password',
            'is_active',
            'role',
            'google_id',
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = (bool) $data['is_active'];
        }

        $user->fill($data)->save();

        return $user;
    }
}

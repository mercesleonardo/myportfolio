<?php

namespace App\Actions\Admin\Users;

use App\Models\User;
use Illuminate\Support\Arr;

final class CreateUser
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data): User
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

        $data['locale'] ??= app()->getLocale();
        $data['is_published'] = false;

        return User::create($data);
    }
}

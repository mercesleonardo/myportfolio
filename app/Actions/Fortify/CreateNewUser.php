<?php

namespace App\Actions\Fortify;

use App\Concerns\{PasswordValidationRules, ProfileValidationRules};
use App\Enums\UserRole;
use App\Models\User;
use App\Support\UsernameSlug;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;
    use ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $input['slug'] = UsernameSlug::normalize((string) ($input['slug'] ?? ''));

        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'name'      => $input['name'],
            'slug'      => $input['slug'],
            'email'     => $input['email'],
            'password'  => $input['password'],
            'is_active' => true,
            'role'      => UserRole::USER,
            'locale'    => app()->getLocale(),
        ]);
    }
}

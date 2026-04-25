<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    /**
     * Words that must never be used as user slugs.
     *
     * @return array<int, string>
     */
    protected function reservedSlugs(): array
    {
        return [
            'admin',
            'api',
            'dashboard',
            'login',
            'logout',
            'register',
            'settings',
        ];
    }

    /**
     * Get the validation rules used to validate user profiles.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function profileRules(?int $userId = null): array
    {
        return [
            'name'  => $this->nameRules(),
            'slug'  => $this->slugRules($userId),
            'email' => $this->emailRules($userId),
        ];
    }

    /**
     * Get the validation rules used to validate user names.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function nameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * Get the validation rules used to validate user emails.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function emailRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            $userId === null
                ? Rule::unique(User::class)
                : Rule::unique(User::class)->ignore($userId),
        ];
    }

    /**
     * Get the validation rules used to validate user slugs.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function slugRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'max:255',
            'regex:/^[a-z]+$/',
            Rule::notIn($this->reservedSlugs()),
            $userId === null
                ? Rule::unique(User::class, 'slug')
                : Rule::unique(User::class, 'slug')->ignore($userId),
        ];
    }
}

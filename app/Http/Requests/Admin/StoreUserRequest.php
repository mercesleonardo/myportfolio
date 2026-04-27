<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use App\Support\UsernameSlug;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z]+$/',
                Rule::notIn(UsernameSlug::reserved()),
                Rule::unique('users', 'slug'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['present', 'boolean'],
            'role'      => ['required', Rule::in(array_map(fn (UserRole $r) => $r->value, UserRole::cases()))],
        ];
    }

    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage-users');
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('slug')) {
            $this->merge([
                'slug' => UsernameSlug::normalize((string) $this->input('slug')),
            ]);
        }

        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}

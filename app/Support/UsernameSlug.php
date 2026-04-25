<?php

namespace App\Support;

use Illuminate\Support\Str;

final class UsernameSlug
{
    /**
     * Normalize a user-provided value into a username slug.
     *
     * Rules: letters only (a-z), lowercase, no accents/special chars.
     */
    public static function normalize(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z]/', '')
            ->toString();
    }

    /**
     * @return array<int, string>
     */
    public static function reserved(): array
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
}

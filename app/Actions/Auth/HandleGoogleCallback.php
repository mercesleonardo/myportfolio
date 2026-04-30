<?php

namespace App\Actions\Auth;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\UsernameSlug;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Contracts\User as SocialiteUser;

final class HandleGoogleCallback
{
    public function handle(SocialiteUser $googleUser): User
    {
        $email    = (string) ($googleUser->getEmail() ?? '');
        $googleId = (string) ($googleUser->getId() ?? '');

        if ($googleId === '' || $email === '') {
            throw ValidationException::withMessages([
                'google' => 'Unable to authenticate with Google.',
            ]);
        }

        $user = User::query()
            ->where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        if ($user !== null) {
            if (filled($user->google_id) === false) {
                $user->forceFill(['google_id' => $googleId])->save();
            }

            return $user;
        }

        $name = (string) ($googleUser->getName() ?: $email);

        $slug = UsernameSlug::normalize($name);

        if ($slug === '') {
            throw ValidationException::withMessages([
                'slug' => 'Please choose a username to continue.',
            ]);
        }

        if (in_array($slug, UsernameSlug::reserved(), true)) {
            throw ValidationException::withMessages([
                'slug' => 'This username is reserved. Please choose another one to continue.',
            ]);
        }

        if (User::query()->where('slug', $slug)->exists()) {
            throw ValidationException::withMessages([
                'slug' => 'This username is already taken. Please choose another one to continue.',
            ]);
        }

        $user = User::create([
            'name'         => $name,
            'slug'         => $slug,
            'email'        => $email,
            'password'     => str()->random(32),
            'is_active'    => true,
            'is_published' => false,
            'role'         => UserRole::USER,
            'google_id'    => $googleId,
            'locale'       => app()->getLocale(),
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();

        return $user;
    }
}

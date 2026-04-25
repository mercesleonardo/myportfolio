<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();

        $email    = (string) ($googleUser->getEmail() ?? '');
        $googleId = (string) ($googleUser->getId() ?? '');

        if ($googleId === '' || $email === '') {
            return redirect()->route('login')->with('status', 'Unable to authenticate with Google.');
        }

        $user = User::query()
            ->where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        if ($user !== null) {
            if (filled($user->google_id) === false) {
                $user->forceFill(['google_id' => $googleId])->save();
            }
        } else {
            $name = (string) ($googleUser->getName() ?: Str::before($email, '@'));

            $slug = Str::of($email)
                ->before('@')
                ->ascii()
                ->lower()
                ->replaceMatches('/[^a-z]/', '')
                ->toString();

            if ($slug === '') {
                return redirect()->route('register')->withErrors([
                    'slug' => 'Please choose a username to continue.',
                ]);
            }

            if (User::query()->where('slug', $slug)->exists()) {
                return redirect()->route('register')->withErrors([
                    'slug' => 'This username is already taken. Please choose another one to continue.',
                ]);
            }

            $user = User::create([
                'name'      => $name,
                'slug'      => $slug,
                'email'     => $email,
                'password'  => Str::random(32),
                'is_active' => true,
                'role'      => UserRole::USER->value,
                'google_id' => $googleId,
                'locale'    => config('app.locale'),
            ]);

            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user, remember: true);

        return redirect()->route('dashboard');
    }
}

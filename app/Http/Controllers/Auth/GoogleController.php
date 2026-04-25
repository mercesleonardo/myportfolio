<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\HandleGoogleCallback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
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

        try {
            $user = app(HandleGoogleCallback::class)->handle($googleUser);
        } catch (ValidationException $e) {
            $messages = $e->errors();

            if (array_key_exists('slug', $messages)) {
                return redirect()->route('register')->withErrors($messages);
            }

            return redirect()->route('login')->with('status', $messages['google'][0] ?? 'Unable to authenticate with Google.');
        }

        Auth::login($user, remember: true);

        return redirect()->route('dashboard');
    }
}

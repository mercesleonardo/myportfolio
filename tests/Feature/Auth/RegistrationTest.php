<?php

use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $token = 'test-csrf-token';

    $response = $this->withSession(['_token' => $token])->post(route('register.store'), [
        '_token'                => $token,
        'name'                  => 'Test User',
        'slug'                  => 'testuser',
        'email'                 => 'test@example.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = \App\Models\User::query()->where('email', 'test@example.com')->firstOrFail();
    expect($user->is_published)->toBeFalse();
});

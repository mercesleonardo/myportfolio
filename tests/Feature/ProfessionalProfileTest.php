<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

test('guest cannot access professional profile page', function () {
    $this->get(route('professional-profile.edit'))->assertRedirect();
});

test('professional title and bio are required', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $token = Str::random(40);
    $this->withSession(['_token' => $token]);

    $response = $this
        ->withHeader('X-CSRF-TOKEN', $token)
        ->post(route('professional-profile.update'), [
            '_token'  => $token,
            '_method' => 'patch',
        ]);

    $response->assertSessionHasErrors(['professional_title', 'bio']);
});

test('user can update own professional profile', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $this->actingAs($user);
    $token = Str::random(40);
    $this->withSession(['_token' => $token]);

    $response = $this
        ->withHeader('X-CSRF-TOKEN', $token)
        ->post(route('professional-profile.update'), [
            '_token'             => $token,
            '_method'            => 'patch',
            'professional_title' => 'PHP Developer',
            'bio'                => 'Test bio',
            'avatar'             => UploadedFile::fake()->image('avatar.jpg', 256, 256),
            'resume'             => UploadedFile::fake()->create('cv.pdf', 120, 'application/pdf'),
        ]);

    $response->assertRedirect(route('professional-profile.edit'));

    $user->refresh();

    expect($user->profile)->not()->toBeNull();
    expect($user->profile->professional_title)->toBe('PHP Developer');
    expect($user->profile->bio)->toBe('Test bio');
    expect($user->profile->avatar_path)->not()->toBeNull();
    expect($user->profile->resume_path)->not()->toBeNull();

    Storage::disk('public')->assertExists((string) $user->profile->avatar_path);
    Storage::disk('public')->assertExists((string) $user->profile->resume_path);
});

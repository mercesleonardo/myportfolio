<?php

use App\Enums\UserRole;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('non-admin cannot access users admin', function () {
    $user = User::factory()->create([
        'role' => UserRole::USER,
        'slug' => 'regularuser',
    ]);

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

test('admin can list users', function () {
    $admin = User::factory()->create([
        'role' => UserRole::ADMIN,
        'slug' => 'adminuser',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('admin/users/Index'));
});

test('admin can filter users by role', function () {
    $admin = User::factory()->create([
        'role' => UserRole::ADMIN,
        'slug' => 'adminfilter',
    ]);

    User::factory()->create(['role' => UserRole::USER, 'slug' => 'filterusera', 'email' => 'a@example.com']);
    User::factory()->create(['role' => UserRole::FINANCE, 'slug' => 'filteruserb', 'email' => 'b@example.com']);

    $this->actingAs($admin)
        ->get(route('admin.users.index', ['role' => UserRole::FINANCE->value]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
            ->component('admin/users/Index')
            ->where('filters.role', UserRole::FINANCE->value)
            ->has('users.data', 1)
        );
});

test('admin can create a user', function () {
    $admin = User::factory()->create([
        'role' => UserRole::ADMIN,
        'slug' => 'admincreator',
    ]);

    $token = 'test-csrf-token';

    $this->actingAs($admin)
        ->withSession(['_token' => $token])
        ->post(route('admin.users.store'), [
            '_token'                => $token,
            'name'                  => 'New User',
            'slug'                  => 'newuser',
            'email'                 => 'newuser@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
            'is_active'             => true,
            'role'                  => UserRole::USER->value,
            'is_published'          => true,
        ])
        ->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', [
        'email'        => 'newuser@example.com',
        'slug'         => 'newuser',
        'is_active'    => 1,
        'is_published' => 0,
    ]);
});

test('admin can create an inactive user', function () {
    $admin = User::factory()->create([
        'role' => UserRole::ADMIN,
        'slug' => 'admincreatorinactive',
    ]);

    $token = 'test-csrf-token';

    $this->actingAs($admin)
        ->withSession(['_token' => $token])
        ->post(route('admin.users.store'), [
            '_token'                => $token,
            'name'                  => 'Inactive User',
            'slug'                  => 'inactiveuser',
            'email'                 => 'inactiveuser@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
            'is_active'             => false,
            'role'                  => UserRole::USER->value,
        ])
        ->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', [
        'email'        => 'inactiveuser@example.com',
        'slug'         => 'inactiveuser',
        'is_active'    => 0,
        'is_published' => 0,
    ]);
});

test('admin can update a user', function () {
    $admin = User::factory()->create([
        'role' => UserRole::ADMIN,
        'slug' => 'adminupdater',
    ]);

    $user = User::factory()->create([
        'role' => UserRole::USER,
        'slug' => 'targetuser',
    ]);

    $token = 'test-csrf-token';

    $this->actingAs($admin)
        ->withSession(['_token' => $token])
        ->patch(route('admin.users.update', $user), [
            '_token'                => $token,
            'name'                  => 'Updated Name',
            'slug'                  => 'updateduser',
            'email'                 => $user->email,
            'password'              => '',
            'password_confirmation' => '',
            'is_active'             => false,
            'role'                  => UserRole::FINANCE->value,
        ])
        ->assertRedirect(route('admin.users.edit', $user));

    $user->refresh();

    expect($user->name)->toBe('Updated Name');
    expect($user->slug)->toBe('updateduser');
    expect($user->is_active)->toBeFalse();
    expect($user->role)->toBe(UserRole::FINANCE);
});

test('admin can delete a user', function () {
    $admin = User::factory()->create([
        'role' => UserRole::ADMIN,
        'slug' => 'admindeleter',
    ]);

    $user = User::factory()->create([
        'role' => UserRole::USER,
        'slug' => 'deleteuser',
    ]);

    $token = 'test-csrf-token';

    $this->actingAs($admin)
        ->withSession(['_token' => $token])
        ->delete(route('admin.users.destroy', $user), ['_token' => $token])
        ->assertRedirect(route('admin.users.index'));

    $this->assertSoftDeleted('users', [
        'id' => $user->id,
    ]);
});

test('admin can restore a user', function () {
    $admin = User::factory()->create([
        'role' => UserRole::ADMIN,
        'slug' => 'adminrestorer',
    ]);

    $user = User::factory()->create([
        'role' => UserRole::USER,
        'slug' => 'restoreuser',
    ]);

    $user->delete();

    $token = 'test-csrf-token';

    $this->actingAs($admin)
        ->withSession(['_token' => $token])
        ->post(route('admin.users.restore', $user), ['_token' => $token])
        ->assertRedirect(route('admin.users.edit', $user));

    expect(User::withTrashed()->find($user->id)?->deleted_at)->toBeNull();
});

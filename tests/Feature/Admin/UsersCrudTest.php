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

    $this->actingAs($admin)
        ->post(route('admin.users.store'), [
            'name'                  => 'New User',
            'slug'                  => 'newuser',
            'email'                 => 'newuser@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
            'is_active'             => true,
            'role'                  => UserRole::USER->value,
        ])
        ->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', [
        'email' => 'newuser@example.com',
        'slug'  => 'newuser',
        'is_active' => 1,
    ]);
});

test('admin can create an inactive user', function () {
    $admin = User::factory()->create([
        'role' => UserRole::ADMIN,
        'slug' => 'admincreatorinactive',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.users.store'), [
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
        'email'     => 'inactiveuser@example.com',
        'slug'      => 'inactiveuser',
        'is_active' => 0,
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

    $this->actingAs($admin)
        ->patch(route('admin.users.update', $user), [
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

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $user))
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

    $this->actingAs($admin)
        ->post(route('admin.users.restore', $user))
        ->assertRedirect(route('admin.users.edit', $user));

    expect(User::withTrashed()->find($user->id)?->deleted_at)->toBeNull();
});

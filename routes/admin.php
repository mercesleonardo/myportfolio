<?php

use App\Http\Controllers\Admin\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'can:manage-users'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function (): void {
        Route::redirect('/', '/admin/users')->name('index');

        Route::bind('user', fn (string $value): User => User::withTrashed()->findOrFail($value));

        Route::post('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');

        Route::resource('users', UserController::class)
            ->except(['show']);
    });

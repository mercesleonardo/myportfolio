<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ProfessionalProfileController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::get('professional-profile', [ProfessionalProfileController::class, 'edit'])
        ->name('professional-profile.edit');
    Route::patch('professional-profile', [ProfessionalProfileController::class, 'update'])
        ->name('professional-profile.update');
});

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

require __DIR__ . '/settings.php';

require __DIR__ . '/admin.php';

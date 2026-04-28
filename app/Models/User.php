<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Attributes\{Fillable, Hidden};
use Illuminate\Database\Eloquent\{Builder, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'slug', 'email', 'password', 'is_active', 'role', 'google_id', 'locale'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements HasLocalePreference, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'       => 'datetime',
            'password'                => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'deleted_at'              => 'datetime',
            'is_active'               => 'boolean',
            'role'                    => UserRole::class,
        ];
    }

    public function preferredLocale(): string
    {
        return filled($this->locale) ? (string) $this->locale : (string) config('app.locale');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isSupport(): bool
    {
        return $this->role === UserRole::SUPPORT;
    }

    public function isFinance(): bool
    {
        return $this->role === UserRole::FINANCE;
    }

    public function isUser(): bool
    {
        return $this->role === UserRole::USER;
    }

    public function isAdminOrSupport(): bool
    {
        return $this->isAdmin() || $this->isSupport();
    }

    public function isAdminOrFinance(): bool
    {
        return $this->isAdmin() || $this->isFinance();
    }

    public function isAdminOrFinanceOrSupport(): bool
    {
        return $this->isAdmin() || $this->isFinance() || $this->isSupport();
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }
}

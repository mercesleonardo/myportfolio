<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN   = 'admin';
    case SUPPORT = 'support';
    case FINANCE = 'finance';
    case USER    = 'user';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN   => 'Admin',
            self::SUPPORT => 'Support',
            self::FINANCE => 'Finance',
            self::USER    => 'User',
        };
    }
}

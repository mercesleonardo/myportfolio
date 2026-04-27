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
            self::ADMIN   => __('Admin'),
            self::SUPPORT => __('Support'),
            self::FINANCE => __('Finance'),
            self::USER    => __('User'),
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\User\Enums;

enum UserRole: string
{
    case SUBSCRIBER = 'subscriber';
    case ADMIN = 'admin';
}

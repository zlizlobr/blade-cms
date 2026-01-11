<?php

declare(strict_types=1);

namespace App\Domain\Tenant\Enums;

enum PlanType: string
{
    case FREE = 'free';
    case BASIC = 'basic';
    case PRO = 'pro';
}

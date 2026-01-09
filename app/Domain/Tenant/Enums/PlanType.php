<?php

namespace App\Domain\Tenant\Enums;

enum PlanType: string
{
    case FREE = 'free';
    case BASIC = 'basic';
    case PRO = 'pro';
}

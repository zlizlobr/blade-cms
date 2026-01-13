<?php

declare(strict_types=1);

namespace App\Domain\Module\Enums;

enum ModuleStatus: string
{
    case INSTALLED = 'installed';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}

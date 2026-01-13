<?php

declare(strict_types=1);

namespace App\Domain\Module\Exceptions;

class ModuleNotFoundException extends ModuleException
{
    public function __construct(string $moduleSlug)
    {
        $message = sprintf("Module '%s' not found", $moduleSlug);

        parent::__construct($message, $moduleSlug);
    }
}

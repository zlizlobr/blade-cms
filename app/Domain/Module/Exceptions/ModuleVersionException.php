<?php

declare(strict_types=1);

namespace App\Domain\Module\Exceptions;

class ModuleVersionException extends ModuleException
{
    public function __construct(
        string $moduleSlug,
        public readonly string $requiredVersion,
        public readonly string $actualVersion
    ) {
        $message = sprintf(
            "Module '%s' version mismatch. Required: %s, Actual: %s",
            $moduleSlug,
            $requiredVersion,
            $actualVersion
        );

        parent::__construct($message, $moduleSlug);
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Module\Exceptions;

class ModuleDependencyException extends ModuleException
{
    /**
     * @param array<string> $missingDependencies
     */
    public function __construct(
        string $moduleSlug,
        public readonly array $missingDependencies
    ) {
        $message = sprintf(
            "Module '%s' has unsatisfied dependencies: %s",
            $moduleSlug,
            implode(', ', $missingDependencies)
        );

        parent::__construct($message, $moduleSlug);
    }
}

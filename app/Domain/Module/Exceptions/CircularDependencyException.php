<?php

declare(strict_types=1);

namespace App\Domain\Module\Exceptions;

class CircularDependencyException extends ModuleException
{
    /**
     * @param array<string> $dependencyChain
     */
    public function __construct(
        public readonly array $dependencyChain
    ) {
        $message = sprintf(
            'Circular dependency detected: %s',
            implode(' -> ', $dependencyChain)
        );

        parent::__construct($message);
    }
}

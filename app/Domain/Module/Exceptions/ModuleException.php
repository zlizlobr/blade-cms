<?php

declare(strict_types=1);

namespace App\Domain\Module\Exceptions;

use Exception;

class ModuleException extends Exception
{
    public function __construct(
        string $message,
        public readonly ?string $moduleSlug = null,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}

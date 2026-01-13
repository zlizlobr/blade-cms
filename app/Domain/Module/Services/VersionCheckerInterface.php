<?php

declare(strict_types=1);

namespace App\Domain\Module\Services;

interface VersionCheckerInterface
{
    /**
     * Check if a version satisfies a constraint.
     *
     * @param string $version Version to check (e.g., "1.2.3")
     * @param string $constraint Constraint to check against (e.g., "^1.0", ">=2.0")
     */
    public function satisfies(string $version, string $constraint): bool;

    /**
     * Check if module version is compatible with core version.
     */
    public function isCompatibleWithCore(string $moduleCompatibility, string $coreVersion): bool;

    /**
     * Parse a version string into components.
     *
     * @return array{major: int, minor: int, patch: int}
     */
    public function parseVersion(string $version): array;
}

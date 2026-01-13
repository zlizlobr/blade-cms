<?php

declare(strict_types=1);

namespace App\Domain\Module\Services;

use App\Domain\Module\ValueObjects\VersionConstraint;

class VersionChecker implements VersionCheckerInterface
{
    /**
     * Check if a version satisfies a constraint.
     */
    public function satisfies(string $version, string $constraint): bool
    {
        $versionConstraint = new VersionConstraint($constraint);

        return $versionConstraint->matches($version);
    }

    /**
     * Check if module version is compatible with core version.
     */
    public function isCompatibleWithCore(string $moduleCompatibility, string $coreVersion): bool
    {
        if (empty($moduleCompatibility)) {
            return true; // No constraint means compatible with all versions
        }

        return $this->satisfies($coreVersion, $moduleCompatibility);
    }

    /**
     * Parse a version string into components.
     *
     * @return array{major: int, minor: int, patch: int}
     */
    public function parseVersion(string $version): array
    {
        // Strip any pre-release or metadata
        $version = preg_replace('/[-+].*$/', '', $version) ?? '';

        $parts = explode('.', $version);

        return [
            'major' => (int) ($parts[0] ?? 0),
            'minor' => (int) ($parts[1] ?? 0),
            'patch' => (int) ($parts[2] ?? 0),
        ];
    }
}

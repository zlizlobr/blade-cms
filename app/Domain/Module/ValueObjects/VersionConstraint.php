<?php

declare(strict_types=1);

namespace App\Domain\Module\ValueObjects;

class VersionConstraint
{
    private string $operator;
    private string $version;

    /**
     * Create a new version constraint.
     *
     * @param string $constraint Full constraint string (e.g., "^1.0", ">=2.3.0", "~1.2")
     */
    public function __construct(string $constraint)
    {
        $this->parseConstraint($constraint);
    }

    /**
     * Parse a constraint string into operator and version.
     */
    private function parseConstraint(string $constraint): void
    {
        $constraint = trim($constraint);

        // Extract operator
        if (preg_match('/^(\^|~|>=|<=|>|<|=)?(.+)$/', $constraint, $matches)) {
            $this->operator = $matches[1] ?: '=';
            $this->version = trim($matches[2]);
        } else {
            $this->operator = '=';
            $this->version = $constraint;
        }
    }

    /**
     * Get the operator.
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * Get the version.
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Check if a version matches this constraint.
     */
    public function matches(string $version): bool
    {
        [$major, $minor, $patch] = $this->parseVersion($version);
        [$constraintMajor, $constraintMinor, $constraintPatch] = $this->parseVersion($this->version);

        return match ($this->operator) {
            '^' => $this->matchesCaret($major, $minor, $patch, $constraintMajor, $constraintMinor, $constraintPatch),
            '~' => $this->matchesTilde($major, $minor, $patch, $constraintMajor, $constraintMinor, $constraintPatch),
            '>=' => $this->compareVersion($major, $minor, $patch, $constraintMajor, $constraintMinor, $constraintPatch) >= 0,
            '<=' => $this->compareVersion($major, $minor, $patch, $constraintMajor, $constraintMinor, $constraintPatch) <= 0,
            '>' => $this->compareVersion($major, $minor, $patch, $constraintMajor, $constraintMinor, $constraintPatch) > 0,
            '<' => $this->compareVersion($major, $minor, $patch, $constraintMajor, $constraintMinor, $constraintPatch) < 0,
            '=' => $this->compareVersion($major, $minor, $patch, $constraintMajor, $constraintMinor, $constraintPatch) === 0,
            default => false,
        };
    }

    /**
     * Parse version string into major, minor, patch.
     */
    private function parseVersion(string $version): array
    {
        // Strip any pre-release or metadata (e.g., "1.2.3-beta" becomes "1.2.3")
        $version = preg_replace('/[-+].*$/', '', $version);

        $parts = explode('.', $version);
        $major = (int) ($parts[0] ?? 0);
        $minor = (int) ($parts[1] ?? 0);
        $patch = (int) ($parts[2] ?? 0);

        return [$major, $minor, $patch];
    }

    /**
     * Match caret operator (^) - compatible version.
     * ^1.2.3 allows >=1.2.3 and <2.0.0
     */
    private function matchesCaret(
        int $major,
        int $minor,
        int $patch,
        int $constraintMajor,
        int $constraintMinor,
        int $constraintPatch
    ): bool {
        // Must be same major version
        if ($major !== $constraintMajor) {
            return false;
        }

        // Check if version is >= constraint
        $comparison = $this->compareVersion($major, $minor, $patch, $constraintMajor, $constraintMinor, $constraintPatch);

        return $comparison >= 0;
    }

    /**
     * Match tilde operator (~) - approximately equivalent.
     * ~1.2.3 allows >=1.2.3 and <1.3.0
     */
    private function matchesTilde(
        int $major,
        int $minor,
        int $patch,
        int $constraintMajor,
        int $constraintMinor,
        int $constraintPatch
    ): bool {
        // Must be same major and minor version
        if ($major !== $constraintMajor || $minor !== $constraintMinor) {
            return false;
        }

        // Patch must be >= constraint patch
        return $patch >= $constraintPatch;
    }

    /**
     * Compare two versions.
     * Returns: -1 if version1 < version2, 0 if equal, 1 if version1 > version2
     */
    private function compareVersion(
        int $major1,
        int $minor1,
        int $patch1,
        int $major2,
        int $minor2,
        int $patch2
    ): int {
        if ($major1 !== $major2) {
            return $major1 <=> $major2;
        }

        if ($minor1 !== $minor2) {
            return $minor1 <=> $minor2;
        }

        return $patch1 <=> $patch2;
    }

    /**
     * Convert constraint back to string.
     */
    public function toString(): string
    {
        return ($this->operator === '=' ? '' : $this->operator).$this->version;
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}

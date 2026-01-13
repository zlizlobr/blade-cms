<?php

declare(strict_types=1);

namespace App\Domain\Module\Services;

use App\Domain\Module\Models\Module;

interface DependencyResolverInterface
{
    /**
     * Resolve all dependencies for a module recursively.
     *
     * @return array<string, string> Array of [slug => version_constraint]
     */
    public function resolveDependencies(Module $module): array;

    /**
     * Check if all dependencies for a module are satisfied (installed and active).
     */
    public function checkDependenciesSatisfied(Module $module): bool;

    /**
     * Detect circular dependency starting from a module slug.
     *
     * @param array<string> $visited Already visited modules in recursion
     */
    public function detectCircularDependency(string $slug, array $visited = []): bool;

    /**
     * Get activation order for multiple modules using topological sort.
     *
     * @param array<Module> $modules Modules to sort
     * @return array<string> Array of module slugs in activation order
     */
    public function getActivationOrder(array $modules): array;
}

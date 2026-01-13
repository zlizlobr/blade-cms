<?php

declare(strict_types=1);

namespace App\Domain\Module\Services;

use App\Domain\Module\Enums\ModuleStatus;
use App\Domain\Module\Exceptions\CircularDependencyException;
use App\Domain\Module\Models\Module;
use App\Domain\Module\Repositories\ModuleRepositoryInterface;

class DependencyResolver implements DependencyResolverInterface
{
    public function __construct(
        private readonly ModuleRepositoryInterface $repository
    ) {}

    /**
     * Resolve all dependencies for a module recursively.
     */
    public function resolveDependencies(Module $module): array
    {
        $dependencies = $module->getDependencies();
        $resolved = [];

        foreach ($dependencies as $slug => $constraint) {
            $resolved[$slug] = $constraint;

            // Recursively resolve dependencies of dependencies
            $dependencyModule = $this->repository->findBySlug($slug);
            if ($dependencyModule) {
                $nestedDeps = $this->resolveDependencies($dependencyModule);
                $resolved = array_merge($resolved, $nestedDeps);
            }
        }

        return $resolved;
    }

    /**
     * Check if all dependencies for a module are satisfied.
     */
    public function checkDependenciesSatisfied(Module $module): bool
    {
        $dependencies = $module->getDependencies();

        foreach ($dependencies as $slug => $constraint) {
            $dependencyModule = $this->repository->findBySlug($slug);

            // Dependency must exist
            if (! $dependencyModule) {
                return false;
            }

            // Dependency must be active
            if (! $dependencyModule->isActive()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Detect circular dependency using DFS with recursion stack.
     */
    public function detectCircularDependency(string $slug, array $visited = []): bool
    {
        // If we've seen this slug in current recursion path, we have a cycle
        if (in_array($slug, $visited, true)) {
            return true;
        }

        $module = $this->repository->findBySlug($slug);
        if (! $module) {
            return false; // Module doesn't exist, no cycle possible
        }

        // Add current module to visited path
        $visited[] = $slug;

        // Check all dependencies
        $dependencies = $module->getDependencies();
        foreach (array_keys($dependencies) as $dependencySlug) {
            if ($this->detectCircularDependency($dependencySlug, $visited)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get activation order using topological sort (Kahn's algorithm).
     */
    public function getActivationOrder(array $modules): array
    {
        if (empty($modules)) {
            return [];
        }

        // Build adjacency list and in-degree map
        $graph = [];
        $inDegree = [];
        $moduleMap = [];

        foreach ($modules as $module) {
            $slug = $module->slug;
            $moduleMap[$slug] = $module;
            $graph[$slug] = array_keys($module->getDependencies());
            $inDegree[$slug] = 0;
        }

        // Calculate in-degrees
        foreach ($graph as $dependencies) {
            foreach ($dependencies as $dependency) {
                if (isset($inDegree[$dependency])) {
                    $inDegree[$dependency]++;
                }
            }
        }

        // Find all nodes with no incoming edges
        $queue = [];
        foreach ($inDegree as $slug => $degree) {
            if ($degree === 0) {
                $queue[] = $slug;
            }
        }

        // Process queue
        $result = [];
        while (! empty($queue)) {
            $slug = array_shift($queue);
            $result[] = $slug;

            // Reduce in-degree for neighbors
            foreach ($graph[$slug] as $dependency) {
                if (isset($inDegree[$dependency])) {
                    $inDegree[$dependency]--;
                    if ($inDegree[$dependency] === 0) {
                        $queue[] = $dependency;
                    }
                }
            }
        }

        // Check for cycles
        if (count($result) !== count($modules)) {
            throw new CircularDependencyException($result);
        }

        // Return in reverse order (dependencies first)
        return array_reverse($result);
    }
}

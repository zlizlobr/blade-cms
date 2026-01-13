<?php

declare(strict_types=1);

namespace App\Domain\Module\Services;

use App\Domain\Module\Models\Module;
use Illuminate\Database\Eloquent\Collection;

interface ModuleServiceInterface
{
    /**
     * Create service instance with default dependencies.
     * Factory method for convenient instantiation outside Laravel container.
     */
    public static function create(): self;

    /**
     * Install a new module.
     *
     * @param array<string, mixed> $metadata Module metadata from module.json
     */
    public function install(string $slug, array $metadata): Module;

    /**
     * Activate a module (checks dependencies and version constraints).
     */
    public function activate(string $slug): Module;

    /**
     * Deactivate a module (checks no active modules depend on it).
     */
    public function deactivate(string $slug): Module;

    /**
     * Uninstall a module (must be inactive first).
     */
    public function uninstall(string $slug): bool;

    /**
     * Get all active modules.
     */
    public function getActiveModules(): Collection;

    /**
     * Get a module by slug.
     */
    public function getModuleBySlug(string $slug): ?Module;

    /**
     * Check if a module can be activated.
     */
    public function canActivate(string $slug): bool;

    /**
     * Check if a module can be deactivated.
     */
    public function canDeactivate(string $slug): bool;

    /**
     * Get dependencies for a module.
     *
     * @return array<string, string> Array of [slug => version_constraint]
     */
    public function getModuleDependencies(string $slug): array;

    /**
     * Get activation order for multiple modules.
     *
     * @param array<string> $slugs Module slugs to activate
     * @return array<string> Slugs in correct activation order
     */
    public function getActivationOrder(array $slugs): array;
}

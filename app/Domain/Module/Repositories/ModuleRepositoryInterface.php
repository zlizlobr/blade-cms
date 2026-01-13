<?php

declare(strict_types=1);

namespace App\Domain\Module\Repositories;

use App\Domain\Module\Enums\ModuleStatus;
use App\Domain\Module\Models\Module;
use Illuminate\Database\Eloquent\Collection;

interface ModuleRepositoryInterface
{
    /**
     * Find a module by its slug.
     */
    public function findBySlug(string $slug): ?Module;

    /**
     * Get all active modules.
     */
    public function findActiveModules(): Collection;

    /**
     * Find modules by status.
     */
    public function findByStatus(ModuleStatus $status): Collection;

    /**
     * Create a new module.
     */
    public function create(array $data): Module;

    /**
     * Update an existing module.
     */
    public function update(Module $module, array $data): bool;

    /**
     * Delete a module.
     */
    public function delete(Module $module): bool;

    /**
     * Check if a module exists by slug.
     */
    public function existsBySlug(string $slug): bool;

    /**
     * Find modules that depend on a given module slug.
     */
    public function findModulesWithDependencyOn(string $slug): Collection;
}

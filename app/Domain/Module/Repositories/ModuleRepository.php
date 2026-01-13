<?php

declare(strict_types=1);

namespace App\Domain\Module\Repositories;

use App\Domain\Module\Enums\ModuleStatus;
use App\Domain\Module\Models\Module;
use Illuminate\Database\Eloquent\Collection;

class ModuleRepository implements ModuleRepositoryInterface
{
    /**
     * Find a module by its slug.
     */
    public function findBySlug(string $slug): ?Module
    {
        return Module::where('slug', $slug)->first();
    }

    /**
     * Get all active modules.
     */
    public function findActiveModules(): Collection
    {
        return Module::where('status', ModuleStatus::ACTIVE)->get();
    }

    /**
     * Find modules by status.
     */
    public function findByStatus(ModuleStatus $status): Collection
    {
        return Module::where('status', $status)->get();
    }

    /**
     * Create a new module.
     */
    public function create(array $data): Module
    {
        return Module::create($data);
    }

    /**
     * Update an existing module.
     */
    public function update(Module $module, array $data): bool
    {
        return $module->update($data);
    }

    /**
     * Delete a module.
     */
    public function delete(Module $module): bool
    {
        return (bool) $module->delete();
    }

    /**
     * Check if a module exists by slug.
     */
    public function existsBySlug(string $slug): bool
    {
        return Module::where('slug', $slug)->exists();
    }

    /**
     * Find modules that depend on a given module slug.
     */
    public function findModulesWithDependencyOn(string $slug): Collection
    {
        return Module::whereJsonContains('dependencies', $slug)->get();
    }
}

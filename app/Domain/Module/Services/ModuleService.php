<?php

declare(strict_types=1);

namespace App\Domain\Module\Services;

use App\Domain\Module\Enums\ModuleStatus;
use App\Domain\Module\Events\ModuleActivated;
use App\Domain\Module\Events\ModuleDeactivated;
use App\Domain\Module\Events\ModuleInstalled;
use App\Domain\Module\Events\ModuleUninstalled;
use App\Domain\Module\Exceptions\ModuleDependencyException;
use App\Domain\Module\Exceptions\ModuleException;
use App\Domain\Module\Exceptions\ModuleNotFoundException;
use App\Domain\Module\Exceptions\ModuleVersionException;
use App\Domain\Module\Models\Module;
use App\Domain\Module\Repositories\ModuleRepository;
use App\Domain\Module\Repositories\ModuleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ModuleService implements ModuleServiceInterface
{
    public function __construct(
        private readonly ModuleRepositoryInterface $repository,
        private readonly DependencyResolverInterface $dependencyResolver,
        private readonly VersionCheckerInterface $versionChecker
    ) {}

    /**
     * Create service instance with default dependencies.
     */
    public static function create(): self
    {
        return new self(
            new ModuleRepository,
            new DependencyResolver(new ModuleRepository),
            new VersionChecker
        );
    }

    /**
     * Install a new module.
     */
    public function install(string $slug, array $metadata): Module
    {
        // Check if module already exists
        if ($this->repository->existsBySlug($slug)) {
            throw new ModuleException("Module '{$slug}' is already installed", $slug);
        }

        // Validate metadata
        $this->validateMetadata($metadata);

        // Check core compatibility
        $coreVersion = config('app.version', '1.0.0');
        $coreCompatibility = $metadata['core_compatibility'] ?? null;

        if ($coreCompatibility && ! $this->versionChecker->isCompatibleWithCore($coreCompatibility, $coreVersion)) {
            throw new ModuleVersionException(
                $slug,
                $coreCompatibility,
                $coreVersion
            );
        }

        // Create module record
        $module = $this->repository->create([
            'name' => $metadata['name'],
            'slug' => $slug,
            'description' => $metadata['description'] ?? null,
            'version' => $metadata['version'],
            'status' => ModuleStatus::INSTALLED,
            'core_compatibility' => $coreCompatibility,
            'dependencies' => $metadata['dependencies'] ?? [],
            'installed_at' => now(),
        ]);

        // Dispatch event
        event(new ModuleInstalled($module));

        return $module;
    }

    /**
     * Activate a module.
     */
    public function activate(string $slug): Module
    {
        $module = $this->repository->findBySlug($slug);

        if (! $module) {
            throw new ModuleNotFoundException($slug);
        }

        // Check if already active
        if ($module->isActive()) {
            return $module;
        }

        // Check if module is in valid state for activation
        if (! $module->isInstalled() && ! $module->isInactive()) {
            throw new ModuleException(
                "Module '{$slug}' must be installed or inactive to be activated",
                $slug
            );
        }

        // Check for circular dependencies
        if ($this->dependencyResolver->detectCircularDependency($slug)) {
            throw new ModuleException(
                "Cannot activate module '{$slug}': circular dependency detected",
                $slug
            );
        }

        // Check dependencies are satisfied
        if (! $this->dependencyResolver->checkDependenciesSatisfied($module)) {
            $missingDeps = $this->getMissingDependencies($module);
            throw new ModuleDependencyException($slug, $missingDeps);
        }

        // Check version constraints for dependencies
        $this->validateDependencyVersions($module);

        // Update module status
        $this->repository->update($module, [
            'status' => ModuleStatus::ACTIVE,
            'enabled_at' => now(),
        ]);

        // Refresh model
        $module->refresh();

        // Dispatch event (triggers hot-reload)
        event(new ModuleActivated($module));

        return $module;
    }

    /**
     * Deactivate a module.
     */
    public function deactivate(string $slug): Module
    {
        $module = $this->repository->findBySlug($slug);

        if (! $module) {
            throw new ModuleNotFoundException($slug);
        }

        // Check if already inactive
        if ($module->isInactive()) {
            return $module;
        }

        // Check if any active modules depend on this one
        $dependentModules = $this->getActiveDependentModules($slug);
        if (! $dependentModules->isEmpty()) {
            $dependentSlugs = $dependentModules->pluck('slug')->toArray();
            throw new ModuleException(
                sprintf(
                    "Cannot deactivate module '%s': active modules depend on it: %s",
                    $slug,
                    implode(', ', $dependentSlugs)
                ),
                $slug
            );
        }

        // Update module status
        $this->repository->update($module, [
            'status' => ModuleStatus::INACTIVE,
            'enabled_at' => null,
        ]);

        // Refresh model
        $module->refresh();

        // Dispatch event
        event(new ModuleDeactivated($module));

        return $module;
    }

    /**
     * Uninstall a module.
     */
    public function uninstall(string $slug): bool
    {
        $module = $this->repository->findBySlug($slug);

        if (! $module) {
            throw new ModuleNotFoundException($slug);
        }

        // Module must be inactive to uninstall
        if ($module->isActive()) {
            throw new ModuleException(
                "Module '{$slug}' must be deactivated before uninstalling",
                $slug
            );
        }

        // Check if any modules depend on this one
        $dependentModules = $this->repository->findModulesWithDependencyOn($slug);
        if (! $dependentModules->isEmpty()) {
            $dependentSlugs = $dependentModules->pluck('slug')->toArray();
            throw new ModuleException(
                sprintf(
                    "Cannot uninstall module '%s': modules depend on it: %s",
                    $slug,
                    implode(', ', $dependentSlugs)
                ),
                $slug
            );
        }

        // Delete module
        $deleted = $this->repository->delete($module);

        if ($deleted) {
            // Dispatch event
            event(new ModuleUninstalled($slug));
        }

        return $deleted;
    }

    /**
     * Get all active modules.
     */
    public function getActiveModules(): Collection
    {
        return $this->repository->findActiveModules();
    }

    /**
     * Get a module by slug.
     */
    public function getModuleBySlug(string $slug): ?Module
    {
        return $this->repository->findBySlug($slug);
    }

    /**
     * Check if a module can be activated.
     */
    public function canActivate(string $slug): bool
    {
        $module = $this->repository->findBySlug($slug);

        if (! $module) {
            return false;
        }

        if ($module->isActive()) {
            return false;
        }

        // Check circular dependencies
        if ($this->dependencyResolver->detectCircularDependency($slug)) {
            return false;
        }

        // Check dependencies satisfied
        if (! $this->dependencyResolver->checkDependenciesSatisfied($module)) {
            return false;
        }

        return true;
    }

    /**
     * Check if a module can be deactivated.
     */
    public function canDeactivate(string $slug): bool
    {
        $module = $this->repository->findBySlug($slug);

        if (! $module) {
            return false;
        }

        if (! $module->isActive()) {
            return false;
        }

        // Check no active modules depend on it
        $dependentModules = $this->getActiveDependentModules($slug);

        return $dependentModules->isEmpty();
    }

    /**
     * Get dependencies for a module.
     */
    public function getModuleDependencies(string $slug): array
    {
        $module = $this->repository->findBySlug($slug);

        if (! $module) {
            return [];
        }

        return $module->getDependencies();
    }

    /**
     * Get activation order for multiple modules.
     */
    public function getActivationOrder(array $slugs): array
    {
        $modules = [];
        foreach ($slugs as $slug) {
            $module = $this->repository->findBySlug($slug);
            if ($module) {
                $modules[] = $module;
            }
        }

        return $this->dependencyResolver->getActivationOrder($modules);
    }

    /**
     * Validate module metadata.
     */
    private function validateMetadata(array $metadata): void
    {
        $required = ['name', 'slug', 'version'];

        foreach ($required as $field) {
            if (! isset($metadata[$field]) || empty($metadata[$field])) {
                throw new ModuleException("Missing required metadata field: {$field}");
            }
        }
    }

    /**
     * Get missing dependencies for a module.
     *
     * @return array<string>
     */
    private function getMissingDependencies(Module $module): array
    {
        $dependencies = $module->getDependencies();
        $missing = [];

        foreach (array_keys($dependencies) as $slug) {
            $dependencyModule = $this->repository->findBySlug($slug);

            if (! $dependencyModule || ! $dependencyModule->isActive()) {
                $missing[] = $slug;
            }
        }

        return $missing;
    }

    /**
     * Validate version constraints for all dependencies.
     */
    private function validateDependencyVersions(Module $module): void
    {
        $dependencies = $module->getDependencies();

        foreach ($dependencies as $slug => $constraint) {
            $dependencyModule = $this->repository->findBySlug($slug);

            if (! $dependencyModule) {
                continue; // Already checked in checkDependenciesSatisfied
            }

            if (! $this->versionChecker->satisfies($dependencyModule->version, $constraint)) {
                throw new ModuleVersionException(
                    $slug,
                    $constraint,
                    $dependencyModule->version
                );
            }
        }
    }

    /**
     * Get active modules that depend on a given module.
     */
    private function getActiveDependentModules(string $slug): Collection
    {
        return $this->repository->findModulesWithDependencyOn($slug)
            ->filter(fn (Module $module) => $module->isActive());
    }
}

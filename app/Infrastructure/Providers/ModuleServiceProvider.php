<?php

declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Domain\Module\Enums\ModuleStatus;
use App\Domain\Module\Models\Module;
use App\Domain\Module\Repositories\ModuleRepository;
use App\Domain\Module\Repositories\ModuleRepositoryInterface;
use App\Domain\Module\Services\DependencyResolver;
use App\Domain\Module\Services\DependencyResolverInterface;
use App\Domain\Module\Services\ModuleService;
use App\Domain\Module\Services\ModuleServiceInterface;
use App\Domain\Module\Services\VersionChecker;
use App\Domain\Module\Services\VersionCheckerInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register repository bindings
        $this->app->bind(
            ModuleRepositoryInterface::class,
            ModuleRepository::class
        );

        // Register service bindings
        $this->app->bind(
            ModuleServiceInterface::class,
            ModuleService::class
        );

        $this->app->bind(
            DependencyResolverInterface::class,
            DependencyResolver::class
        );

        $this->app->bind(
            VersionCheckerInterface::class,
            VersionChecker::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load active modules on application boot
        $this->loadActiveModules();
    }

    /**
     * Load all active modules and register their service providers.
     */
    private function loadActiveModules(): void
    {
        // Skip during migrations or if table doesn't exist yet
        if (! Schema::hasTable('modules')) {
            return;
        }

        try {
            // Get all active modules
            $activeModules = Module::where('status', ModuleStatus::ACTIVE)->get();

            foreach ($activeModules as $module) {
                // Check if module was deactivated in cache (not yet reflected in DB)
                if (Cache::has("module.deactivated.{$module->slug}")) {
                    continue;
                }

                $this->registerModuleProvider($module);
            }
        } catch (\Throwable $e) {
            // Silently fail during early bootstrap (e.g., migrations)
            return;
        }
    }

    /**
     * Register a module's service provider.
     */
    private function registerModuleProvider(Module $module): void
    {
        // Convert slug to PascalCase (e.g., 'blog' -> 'Blog', 'advanced-forms' -> 'AdvancedForms')
        $moduleClass = str_replace('-', '', ucwords($module->slug, '-'));
        $providerClass = "App\\Modules\\{$moduleClass}\\Providers\\ModuleServiceProvider";

        if (class_exists($providerClass)) {
            $this->app->register($providerClass);
        }
    }
}

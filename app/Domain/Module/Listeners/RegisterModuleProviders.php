<?php

declare(strict_types=1);

namespace App\Domain\Module\Listeners;

use App\Domain\Module\Events\ModuleActivated;
use Illuminate\Support\Facades\Log;

class RegisterModuleProviders
{
    /**
     * Handle the module activated event.
     * Registers the module's service provider immediately (hot-reload).
     */
    public function handle(ModuleActivated $event): void
    {
        $module = $event->module;

        // Convert slug to PascalCase (e.g., 'blog' -> 'Blog', 'advanced-forms' -> 'AdvancedForms')
        $moduleClass = str_replace('-', '', ucwords($module->slug, '-'));
        $providerClass = "App\\Modules\\{$moduleClass}\\Providers\\ModuleServiceProvider";

        if (! class_exists($providerClass)) {
            Log::warning("Module provider not found for '{$module->slug}': {$providerClass}");

            return;
        }

        try {
            // Register provider immediately for hot-reload
            app()->register($providerClass);

            Log::info("Module activated and provider registered: {$module->slug}");
        } catch (\Throwable $e) {
            Log::error("Failed to register module provider for '{$module->slug}': {$e->getMessage()}");
        }
    }
}

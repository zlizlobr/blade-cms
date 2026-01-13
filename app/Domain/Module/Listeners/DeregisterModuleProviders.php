<?php

declare(strict_types=1);

namespace App\Domain\Module\Listeners;

use App\Domain\Module\Events\ModuleDeactivated;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DeregisterModuleProviders
{
    /**
     * Handle the module deactivated event.
     * Laravel doesn't support unregistering providers at runtime,
     * so we cache the deactivation flag for the next request.
     */
    public function handle(ModuleDeactivated $event): void
    {
        $module = $event->module;

        // Store deactivation flag in cache
        // ModuleServiceProvider will skip loading this module on next boot
        Cache::put(
            "module.deactivated.{$module->slug}",
            true,
            now()->addYear()
        );

        Log::info("Module deactivated (will take effect on next request): {$module->slug}");
    }
}

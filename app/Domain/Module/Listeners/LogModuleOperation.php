<?php

declare(strict_types=1);

namespace App\Domain\Module\Listeners;

use App\Domain\Module\Events\ModuleActivated;
use App\Domain\Module\Events\ModuleDeactivated;
use App\Domain\Module\Events\ModuleInstalled;
use App\Domain\Module\Events\ModuleUninstalled;
use Illuminate\Support\Facades\Log;

class LogModuleOperation
{
    /**
     * Handle module installed event.
     */
    public function handle(
        ModuleInstalled|ModuleActivated|ModuleDeactivated|ModuleUninstalled $event
    ): void {
        $operation = match (true) {
            $event instanceof ModuleInstalled => 'installed',
            $event instanceof ModuleActivated => 'activated',
            $event instanceof ModuleDeactivated => 'deactivated',
            $event instanceof ModuleUninstalled => 'uninstalled',
        };

        $slug = $event instanceof ModuleUninstalled
            ? $event->slug
            : $event->module->slug;

        $context = [
            'module_slug' => $slug,
            'operation' => $operation,
            'user_id' => auth()->id(),
            'tenant_id' => app()->bound('tenant.id') ? app('tenant.id') : null,
        ];

        if ($event instanceof ModuleInstalled || $event instanceof ModuleActivated || $event instanceof ModuleDeactivated) {
            $context['module_id'] = $event->module->id;
            $context['module_version'] = $event->module->version;
        }

        Log::info("Module operation: {$operation}", $context);
    }
}

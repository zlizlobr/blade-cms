<?php

declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Domain\FormSubmission\Events\FormSubmitted;
use App\Domain\FormSubmission\Listeners\SendFormNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        FormSubmitted::class => [
            SendFormNotification::class,
        ],

        // Module events
        \App\Domain\Module\Events\ModuleInstalled::class => [
            \App\Domain\Module\Listeners\LogModuleOperation::class,
        ],
        \App\Domain\Module\Events\ModuleActivated::class => [
            \App\Domain\Module\Listeners\RegisterModuleProviders::class,
            \App\Domain\Module\Listeners\LogModuleOperation::class,
        ],
        \App\Domain\Module\Events\ModuleDeactivated::class => [
            \App\Domain\Module\Listeners\DeregisterModuleProviders::class,
            \App\Domain\Module\Listeners\LogModuleOperation::class,
        ],
        \App\Domain\Module\Events\ModuleUninstalled::class => [
            \App\Domain\Module\Listeners\LogModuleOperation::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

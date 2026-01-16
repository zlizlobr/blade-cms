<?php

declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Admin\Sidebar\SidebarRegistryInterface;
use Illuminate\Support\ServiceProvider;

class AdminSidebarServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(SidebarRegistryInterface $sidebar): void
    {
        $sidebar->add([
            'label' => 'Dashboard',
            'route' => 'admin.dashboard',
            'icon' => 'home',
            'order' => 10,
        ]);

        $sidebar->add([
            'label' => 'Submissions',
            'route' => 'admin.submissions.index',
            'icon' => 'inbox',
            'order' => 20,
        ]);

        $sidebar->add([
            'label' => 'Modules',
            'route' => 'admin.modules.index',
            'icon' => 'puzzle',
            'order' => 90,
        ]);
    }
}

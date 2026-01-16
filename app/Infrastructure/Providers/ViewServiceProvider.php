<?php

declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Admin\Sidebar\SidebarRegistryInterface;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
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
    public function boot(): void
    {
        // Register theme namespace
        View::addNamespace('theme', resource_path('views/themes/default'));

        // Register admin namespace
        View::addNamespace('admin', resource_path('views/admin'));

        // Register public namespace
        View::addNamespace('public', resource_path('views/public'));

        // Admin sidebar view composer (support both namespace variants)
        View::composer([
            'admin.partials.admin-sidebar',
            'admin::partials.admin-sidebar',
            'partials.admin-sidebar',
        ], function ($view): void {
            $sidebar = $this->app->make(SidebarRegistryInterface::class);
            $view->with('sidebarItems', $sidebar->all());
        });
    }
}

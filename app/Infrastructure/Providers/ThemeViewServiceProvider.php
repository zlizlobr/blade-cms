<?php

declare(strict_types=1);

namespace App\Infrastructure\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ThemeViewServiceProvider extends ServiceProvider
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
    }
}

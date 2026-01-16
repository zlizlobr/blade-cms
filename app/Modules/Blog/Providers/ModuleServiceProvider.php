<?php

declare(strict_types=1);

namespace App\Modules\Blog\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register module config
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/blog.php',
            'blog'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../Views', 'blog');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');

        // Publish config (optional)
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../Config/blog.php' => config_path('blog.php'),
            ], 'blog-config');
        }
    }
}

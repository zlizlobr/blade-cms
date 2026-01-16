<?php

declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Domain\Dashboard\Services\DashboardService;
use App\Domain\Dashboard\Services\DashboardServiceInterface;
use App\Domain\FormSubmission\Repositories\FormSubmissionRepository;
use App\Domain\FormSubmission\Repositories\FormSubmissionRepositoryInterface;
use App\Domain\FormSubmission\Services\FormSubmissionService;
use App\Domain\FormSubmission\Services\FormSubmissionServiceInterface;
use App\Domain\FormSubmission\Services\SubmissionQueryService;
use App\Domain\FormSubmission\Services\SubmissionQueryServiceInterface;
use App\Admin\Sidebar\SidebarRegistry;
use App\Admin\Sidebar\SidebarRegistryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(
            FormSubmissionRepositoryInterface::class,
            FormSubmissionRepository::class
        );

        // Service bindings
        $this->app->bind(
            FormSubmissionServiceInterface::class,
            FormSubmissionService::class
        );

        $this->app->bind(
            SubmissionQueryServiceInterface::class,
            SubmissionQueryService::class
        );

        $this->app->bind(
            DashboardServiceInterface::class,
            DashboardService::class
        );

        // Admin Sidebar Registry (singleton - jedna instance pro celou aplikaci)
        $this->app->singleton(
            SidebarRegistryInterface::class,
            SidebarRegistry::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

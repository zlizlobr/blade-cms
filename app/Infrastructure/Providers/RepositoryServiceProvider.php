<?php

namespace App\Infrastructure\Providers;

use App\Domain\FormSubmission\Repositories\FormSubmissionRepository;
use App\Domain\FormSubmission\Repositories\FormSubmissionRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            FormSubmissionRepositoryInterface::class,
            FormSubmissionRepository::class
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

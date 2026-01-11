<?php

declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Domain\FormSubmission\Models\FormSubmission;
use App\Domain\FormSubmission\Policies\FormSubmissionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        FormSubmission::class => FormSubmissionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}

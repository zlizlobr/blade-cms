<?php

declare(strict_types=1);

return [
    App\Providers\AppServiceProvider::class,
    App\Infrastructure\Providers\AuthServiceProvider::class,
    App\Infrastructure\Providers\EventServiceProvider::class,
    App\Infrastructure\Providers\RepositoryServiceProvider::class,
    App\Infrastructure\Providers\ViewServiceProvider::class,
    App\Infrastructure\Providers\ModuleServiceProvider::class,
];

<?php

declare(strict_types=1);

namespace App\Domain\Module\Policies;

use App\Domain\Module\Models\Module;
use App\Domain\User\Models\User;

class ModulePolicy
{
    /**
     * Determine whether the user can view any modules.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the module.
     */
    public function view(User $user, Module $module): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can activate modules.
     */
    public function activate(User $user, Module $module): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can deactivate modules.
     */
    public function deactivate(User $user, Module $module): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can install modules.
     */
    public function install(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can uninstall modules.
     */
    public function uninstall(User $user, Module $module): bool
    {
        return $user->isAdmin();
    }
}

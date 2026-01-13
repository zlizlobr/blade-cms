<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\Module\Services\ModuleServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ModuleController extends Controller
{
    public function __construct(
        private readonly ModuleServiceInterface $moduleService
    ) {}

    /**
     * Display a listing of all modules.
     */
    public function index(): View
    {
        $activeModules = $this->moduleService->getActiveModules();
        $allModules = app(\App\Domain\Module\Repositories\ModuleRepositoryInterface::class)
            ->findByStatus(\App\Domain\Module\Enums\ModuleStatus::INSTALLED)
            ->merge($activeModules)
            ->merge(
                app(\App\Domain\Module\Repositories\ModuleRepositoryInterface::class)
                    ->findByStatus(\App\Domain\Module\Enums\ModuleStatus::INACTIVE)
            )
            ->unique('id')
            ->sortBy('name');

        return view('admin::modules.index', [
            'modules' => $allModules,
        ]);
    }

    /**
     * Activate a module.
     */
    public function activate(string $slug): RedirectResponse
    {
        try {
            $module = $this->moduleService->activate($slug);

            return redirect()
                ->route('admin.modules.index')
                ->with('success', "Module '{$module->name}' has been activated successfully.");
        } catch (\App\Domain\Module\Exceptions\ModuleDependencyException $e) {
            return redirect()
                ->route('admin.modules.index')
                ->with('error', "Cannot activate module: {$e->getMessage()}");
        } catch (\App\Domain\Module\Exceptions\ModuleException $e) {
            return redirect()
                ->route('admin.modules.index')
                ->with('error', "Error activating module: {$e->getMessage()}");
        }
    }

    /**
     * Deactivate a module.
     */
    public function deactivate(string $slug): RedirectResponse
    {
        try {
            $module = $this->moduleService->deactivate($slug);

            return redirect()
                ->route('admin.modules.index')
                ->with('success', "Module '{$module->name}' has been deactivated successfully.");
        } catch (\App\Domain\Module\Exceptions\ModuleException $e) {
            return redirect()
                ->route('admin.modules.index')
                ->with('error', "Error deactivating module: {$e->getMessage()}");
        }
    }

    /**
     * Show module details.
     */
    public function show(string $slug): View
    {
        $module = $this->moduleService->getModuleBySlug($slug);

        if (! $module) {
            abort(404, 'Module not found');
        }

        $dependencies = $this->moduleService->getModuleDependencies($slug);
        $canActivate = $this->moduleService->canActivate($slug);
        $canDeactivate = $this->moduleService->canDeactivate($slug);

        return view('admin::modules.show', [
            'module' => $module,
            'dependencies' => $dependencies,
            'canActivate' => $canActivate,
            'canDeactivate' => $canDeactivate,
        ]);
    }
}

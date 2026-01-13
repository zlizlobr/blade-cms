<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Api;

use App\Domain\Module\Services\ModuleServiceInterface;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Resources\ModuleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ModuleController extends Controller
{
    public function __construct(
        private readonly ModuleServiceInterface $moduleService
    ) {}

    /**
     * List all modules with optional status filter.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $status = $request->query('status');

        if ($status) {
            $statusEnum = \App\Domain\Module\Enums\ModuleStatus::from($status);
            $modules = app(\App\Domain\Module\Repositories\ModuleRepositoryInterface::class)
                ->findByStatus($statusEnum);
        } else {
            $modules = app(\App\Domain\Module\Repositories\ModuleRepositoryInterface::class)
                ->findByStatus(\App\Domain\Module\Enums\ModuleStatus::INSTALLED)
                ->merge($this->moduleService->getActiveModules())
                ->merge(
                    app(\App\Domain\Module\Repositories\ModuleRepositoryInterface::class)
                        ->findByStatus(\App\Domain\Module\Enums\ModuleStatus::INACTIVE)
                )
                ->unique('id')
                ->sortBy('name');
        }

        return ModuleResource::collection($modules);
    }

    /**
     * Get a specific module by slug.
     */
    public function show(string $slug): ModuleResource|JsonResponse
    {
        $module = $this->moduleService->getModuleBySlug($slug);

        if (! $module) {
            return response()->json([
                'message' => 'Module not found',
            ], 404);
        }

        return new ModuleResource($module);
    }

    /**
     * Install a new module.
     */
    public function store(Request $request): ModuleResource|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:modules,slug',
            'version' => 'required|string|max:20',
            'description' => 'nullable|string',
            'core_compatibility' => 'nullable|string|max:20',
            'dependencies' => 'nullable|array',
        ]);

        try {
            $module = $this->moduleService->install(
                $validated['slug'],
                $validated
            );

            return (new ModuleResource($module))
                ->response()
                ->setStatusCode(201);
        } catch (\App\Domain\Module\Exceptions\ModuleException $e) {
            return response()->json([
                'message' => 'Failed to install module',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Activate a module.
     */
    public function activate(string $slug): ModuleResource|JsonResponse
    {
        try {
            $module = $this->moduleService->activate($slug);

            return new ModuleResource($module);
        } catch (\App\Domain\Module\Exceptions\ModuleDependencyException $e) {
            return response()->json([
                'message' => 'Cannot activate module',
                'error' => $e->getMessage(),
            ], 422);
        } catch (\App\Domain\Module\Exceptions\ModuleException $e) {
            return response()->json([
                'message' => 'Failed to activate module',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Deactivate a module.
     */
    public function deactivate(string $slug): ModuleResource|JsonResponse
    {
        try {
            $module = $this->moduleService->deactivate($slug);

            return new ModuleResource($module);
        } catch (\App\Domain\Module\Exceptions\ModuleException $e) {
            return response()->json([
                'message' => 'Failed to deactivate module',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Check if a module can be activated.
     */
    public function canActivate(string $slug): JsonResponse
    {
        $canActivate = $this->moduleService->canActivate($slug);

        return response()->json([
            'can_activate' => $canActivate,
        ]);
    }

    /**
     * Check if a module can be deactivated.
     */
    public function canDeactivate(string $slug): JsonResponse
    {
        $canDeactivate = $this->moduleService->canDeactivate($slug);

        return response()->json([
            'can_deactivate' => $canDeactivate,
        ]);
    }

    /**
     * Get module dependencies.
     */
    public function dependencies(string $slug): JsonResponse
    {
        $module = $this->moduleService->getModuleBySlug($slug);

        if (! $module) {
            return response()->json([
                'message' => 'Module not found',
            ], 404);
        }

        $dependencies = $this->moduleService->getModuleDependencies($slug);

        return response()->json([
            'dependencies' => $dependencies,
        ]);
    }
}

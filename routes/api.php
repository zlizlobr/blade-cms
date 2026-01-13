<?php

declare(strict_types=1);

use App\Presentation\Http\Controllers\Api\ModuleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/

Route::prefix('v1')->middleware(['auth', 'admin'])->group(function () {
    // Modules API
    Route::prefix('modules')->name('api.modules.')->group(function () {
        Route::get('/', [ModuleController::class, 'index'])->name('index');
        Route::post('/', [ModuleController::class, 'store'])->name('store');
        Route::get('/{slug}', [ModuleController::class, 'show'])->name('show');
        Route::post('/{slug}/activate', [ModuleController::class, 'activate'])->name('activate');
        Route::post('/{slug}/deactivate', [ModuleController::class, 'deactivate'])->name('deactivate');
        Route::get('/{slug}/can-activate', [ModuleController::class, 'canActivate'])->name('can-activate');
        Route::get('/{slug}/can-deactivate', [ModuleController::class, 'canDeactivate'])->name('can-deactivate');
        Route::get('/{slug}/dependencies', [ModuleController::class, 'dependencies'])->name('dependencies');
    });
});

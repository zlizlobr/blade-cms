<?php

use App\Http\Controllers\ProfileController;
use App\Presentation\Http\Controllers\Web\FormSubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public form submission endpoint
Route::post('/contact', [FormSubmissionController::class, 'store'])->name('forms.submit');

// Admin routes - requires authentication and admin role
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    // Admin routes will be added here in upcoming tasks
    // Example: Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';

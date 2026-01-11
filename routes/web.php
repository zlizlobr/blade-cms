<?php

use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Presentation\Http\Controllers\Admin\DashboardController;
use App\Presentation\Http\Controllers\Admin\SubmissionController;
use App\Presentation\Http\Controllers\Web\FormSubmissionController;
use App\Presentation\Http\Controllers\Web\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/', [HomeController::class, 'index'])->name('home');

// Language switcher
Route::post('/locale/{locale}', [LocaleController::class, 'change'])->name('locale.change');

// Language switcher
Route::post('/locale/{locale}', [LocaleController::class, 'change'])->name('locale.change');

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

// Public form submission endpoint
Route::post('/contact', [FormSubmissionController::class, 'store'])->name('forms.submit');

// Admin routes - requires authentication and admin role
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Submissions management
    Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', \App\Http\Controllers\DashboardController::class)
    ->middleware(['auth', 'verified', 'onboarding'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/onboarding', [\App\Http\Controllers\OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding', [\App\Http\Controllers\OnboardingController::class, 'store'])->name('onboarding.store');

    // Skin Analysis & Tracker
    Route::get('/skin-check', [\App\Http\Controllers\SkinAnalysisController::class, 'index'])->name('skin-check.index');
    Route::post('/skin-check', [\App\Http\Controllers\SkinAnalysisController::class, 'store'])->name('skin-check.store');
    Route::get('/analysis/{analysis}/process', [\App\Http\Controllers\SkinAnalysisController::class, 'process'])->name('analysis.process');
    Route::get('/analysis/{analysis}', [\App\Http\Controllers\SkinAnalysisController::class, 'show'])->name('analysis.show');
    Route::get('/analysis', function () { return redirect()->route('skin-check.index'); })->name('analysis.index');
    Route::get('/tracker', [\App\Http\Controllers\TrackerController::class, 'index'])->name('tracker.index');

    // Skin Journal
    Route::get('/journal', [\App\Http\Controllers\SkinJournalController::class, 'index'])->name('journal.index');
    Route::get('/journal/create', [\App\Http\Controllers\SkinJournalController::class, 'create'])->name('journal.create');
    Route::post('/journal', [\App\Http\Controllers\SkinJournalController::class, 'store'])->name('journal.store');

    // Products Catalog
    Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
    Route::post('/products/{product}/add-to-collection', [\App\Http\Controllers\ProductController::class, 'addToCollection'])->name('products.add-to-collection');

    // Routines
    Route::get('/routines', [\App\Http\Controllers\RoutineController::class, 'index'])->name('routines.index');
    Route::post('/routines/{routine}/complete', [\App\Http\Controllers\RoutineController::class, 'complete'])->name('routines.complete');
    Route::get('/routines/{routine}/edit', [\App\Http\Controllers\RoutineController::class, 'edit'])->name('routines.edit');
    Route::post('/routines/{routine}/items', [\App\Http\Controllers\RoutineController::class, 'addItem'])->name('routines.items.add');
    Route::delete('/routines/{routine}/items/{item}', [\App\Http\Controllers\RoutineController::class, 'removeItem'])->name('routines.items.remove');

    // Gamification & Rewards
    Route::get('/rewards', [\App\Http\Controllers\RewardController::class, 'index'])->name('rewards.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';

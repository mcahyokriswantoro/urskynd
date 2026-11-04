<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredProducts = \Illuminate\Support\Facades\Schema::hasTable('products') 
        ? \App\Models\Product::with('category')->where('status', 'active')->take(4)->get()
        : collect();
    $latestArticles = \Illuminate\Support\Facades\Schema::hasTable('articles')
        ? \App\Models\Article::published()->take(3)->get()
        : collect();
    return view('welcome', compact('featuredProducts', 'latestArticles'));
})->name('home');

// Public Skin Check Landing & Preview
Route::get('/skin-check', [\App\Http\Controllers\SkinAnalysisController::class, 'index'])->name('skin-check.index');

// Public Products Catalog & Details
Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
Route::post('/products/{product}/order', [\App\Http\Controllers\ProductController::class, 'orderProduct'])->name('products.order');

// Public Product Purchase Details / Landing Order Page (DVN Reference Style)
Route::get('/detail-pembelian', [\App\Http\Controllers\OrderLandingController::class, 'index'])->name('pembelian.index');
Route::post('/detail-pembelian/order', [\App\Http\Controllers\OrderLandingController::class, 'storeOrder'])->name('pembelian.order');
Route::get('/detail-pembelian/wa-order', [\App\Http\Controllers\OrderLandingController::class, 'quickWhatsappOrder'])->name('pembelian.wa-order');

// Public Consultation Menu & Submission
Route::get('/konsultasi', [\App\Http\Controllers\ConsultationController::class, 'index'])->name('consultation.index');
Route::post('/konsultasi', [\App\Http\Controllers\ConsultationController::class, 'store'])->name('consultation.store');

// Public News & Skincare Articles Menu
Route::get('/berita', [\App\Http\Controllers\NewsController::class, 'index'])->name('news.index');
Route::get('/berita/{slug}', [\App\Http\Controllers\NewsController::class, 'show'])->name('news.show');

// Public Contact Feature
Route::get('/kontak', [\App\Http\Controllers\ContactController::class, 'index'])->name('contact.index');
Route::post('/kontak', [\App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');

// Authenticated User Dashboard
Route::get('/dashboard', \App\Http\Controllers\DashboardController::class)
    ->middleware(['auth', 'verified', 'onboarding'])
    ->name('dashboard');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/onboarding', [\App\Http\Controllers\OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding', [\App\Http\Controllers\OnboardingController::class, 'store'])->name('onboarding.store');

    // Skin Analysis & Tracker (Lanjutan & Analisis Lengkap requires auth)
    Route::post('/skin-check', [\App\Http\Controllers\SkinAnalysisController::class, 'store'])->name('skin-check.store');
    Route::get('/analysis/{analysis}/process', [\App\Http\Controllers\SkinAnalysisController::class, 'process'])->name('analysis.process');
    Route::get('/analysis/{analysis}', [\App\Http\Controllers\SkinAnalysisController::class, 'show'])->name('analysis.show');
    Route::get('/analysis', function () { return redirect()->route('skin-check.index'); })->name('analysis.index');
    Route::get('/tracker', [\App\Http\Controllers\TrackerController::class, 'index'])->name('tracker.index');

    // Skin Journal
    Route::get('/journal', [\App\Http\Controllers\SkinJournalController::class, 'index'])->name('journal.index');
    Route::get('/journal/create', [\App\Http\Controllers\SkinJournalController::class, 'create'])->name('journal.create');
    Route::post('/journal', [\App\Http\Controllers\SkinJournalController::class, 'store'])->name('journal.store');

    // Products - Add to collection (requires auth)
    Route::post('/products/{product}/add-to-collection', [\App\Http\Controllers\ProductController::class, 'addToCollection'])->name('products.add-to-collection');

    // Routines
    Route::get('/routines', [\App\Http\Controllers\RoutineController::class, 'index'])->name('routines.index');
    Route::post('/routines/{routine}/complete', [\App\Http\Controllers\RoutineController::class, 'complete'])->name('routines.complete');
    Route::get('/routines/{routine}/edit', [\App\Http\Controllers\RoutineController::class, 'edit'])->name('routines.edit');
    Route::post('/routines/{routine}/items', [\App\Http\Controllers\RoutineController::class, 'addItem'])->name('routines.items.add');
    Route::delete('/routines/{routine}/items/{item}', [\App\Http\Controllers\RoutineController::class, 'removeItem'])->name('routines.items.remove');

    // Gamification & Rewards
    Route::get('/rewards', [\App\Http\Controllers\RewardController::class, 'index'])->name('rewards.index');

    // Mitra & Afiliator (Komisi & Withdraw)
    Route::get('/affiliate', [\App\Http\Controllers\AffiliateController::class, 'index'])->name('affiliate.index');
    Route::post('/affiliate/join', [\App\Http\Controllers\AffiliateController::class, 'join'])->name('affiliate.join');
    Route::patch('/affiliate/bank', [\App\Http\Controllers\AffiliateController::class, 'updateBank'])->name('affiliate.bank.update');
    Route::post('/affiliate/sales', [\App\Http\Controllers\AffiliateController::class, 'recordSale'])->name('affiliate.sales.store');
    Route::post('/affiliate/withdraw', [\App\Http\Controllers\AffiliateController::class, 'requestWithdrawal'])->name('affiliate.withdraw');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('dashboard');

    // Detail Pembelian page editor
    Route::get('/detail-pembelian', [\App\Http\Controllers\AdminPurchasePageController::class, 'edit'])->name('pembelian.edit');
    Route::put('/detail-pembelian', [\App\Http\Controllers\AdminPurchasePageController::class, 'update'])->name('pembelian.update');
    Route::post('/detail-pembelian/reset', [\App\Http\Controllers\AdminPurchasePageController::class, 'reset'])->name('pembelian.reset');

    // Products Management
    Route::resource('products', \App\Http\Controllers\AdminProductController::class)->except(['show']);

    // Articles / News Management
    Route::resource('articles', \App\Http\Controllers\AdminArticleController::class)->except(['show']);

    // Doctors & Consultations Management
    Route::resource('doctors', \App\Http\Controllers\AdminDoctorController::class)->except(['show']);
    Route::patch('/bookings/{booking}/status', [\App\Http\Controllers\AdminDoctorController::class, 'updateBooking'])->name('bookings.status');

    // Mitra / Affiliates & Commissions Management
    Route::get('/affiliates', [\App\Http\Controllers\AdminAffiliateController::class, 'index'])->name('affiliates.index');
    Route::post('/affiliates/sales', [\App\Http\Controllers\AdminAffiliateController::class, 'storeSale'])->name('affiliates.sales.store');
    Route::patch('/affiliates/sales/{sale}/status', [\App\Http\Controllers\AdminAffiliateController::class, 'updateSaleStatus'])->name('affiliates.sales.status');
    Route::patch('/affiliates/withdrawals/{withdrawal}/status', [\App\Http\Controllers\AdminAffiliateController::class, 'updateWithdrawalStatus'])->name('affiliates.withdrawals.status');
    Route::patch('/affiliates/products/{product}/commission', [\App\Http\Controllers\AdminAffiliateController::class, 'updateProductCommission'])->name('affiliates.products.commission');
    Route::patch('/affiliates/{user}', [\App\Http\Controllers\AdminAffiliateController::class, 'updateAffiliate'])->name('affiliates.update');
});

require __DIR__.'/auth.php';

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerAuthController;
use App\Http\Controllers\SellerDashboardController;
use App\Http\Controllers\SellerProductController;
use App\Http\Controllers\SellerOrderController;
use App\Http\Controllers\SellerProfileController;
use App\Http\Controllers\OrderTrackingController;

// ---------------- Public Routes ----------------

// Public Home Page
Route::get('/', function () {
    return view('welcome');
});

// ✅ Public Order Tracking (No auth, outside protected group)
Route::get('/tracking/order/{id}', [OrderTrackingController::class, 'show'])->name('tracking.order');

// ---------------- Seller Authentication ----------------
Route::prefix('seller')->group(function () {
    Route::get('/login', [SellerAuthController::class, 'showLoginForm'])->name('seller.login');
    Route::post('/login', [SellerAuthController::class, 'login']);
    Route::post('/logout', [SellerAuthController::class, 'logout'])->name('seller.logout');
});

// ---------------- Protected Seller Routes ----------------
Route::middleware(['auth:seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::get('/products/export', [SellerProductController::class, 'export'])->name('products.export');
    Route::resource('products', SellerProductController::class);

    // Orders
    Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{id}', [SellerOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{id}', [SellerOrderController::class, 'update'])->name('orders.update');
    Route::get('/orders/export', [SellerOrderController::class, 'export'])->name('orders.export');

    // Profile
    Route::get('/profile', [SellerProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [SellerProfileController::class, 'update'])->name('profile.update');
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerAuthController;
use App\Http\Controllers\SellerDashboardController;
use App\Http\Controllers\SellerProductController;
use App\Http\Controllers\SellerOrderController;
use App\Http\Controllers\SellerProfileController;
use App\Http\Controllers\OrderTrackingController; // Add this if you create the controller

// Public Home Page
Route::get('/', function () {
    return view('welcome');
});

// ---------------- Public Order Tracking ----------------
// No authentication required
Route::get('/tracking/order/{id}', function ($id) {
    return view('public.order-tracking', ['order_id' => $id]);
})->name('tracking.order');

// Optional: Use a controller later
// Route::get('/tracking/order/{id}', [OrderTrackingController::class, 'show'])->name('tracking.order');

// ---------------- Seller Authentication ----------------
Route::prefix('seller')->group(function () {
    Route::get('/login', [SellerAuthController::class, 'showLoginForm'])->name('seller.login');
    Route::post('/login', [SellerAuthController::class, 'login']);
    Route::post('/logout', [SellerAuthController::class, 'logout'])->name('seller.logout');
});
// ---------------- Protected Seller Routes ----------------
Route::middleware(['auth:seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');

    // ✅ Add export route before resource
    Route::get('/products/export', [SellerProductController::class, 'export'])->name('products.export');

    // ✅ Resource routes (keep this)
    Route::resource('products', SellerProductController::class);

    Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{id}', [SellerOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{id}', [SellerOrderController::class, 'update'])->name('orders.update');
    Route::get('/orders/export', [SellerOrderController::class, 'export'])->name('orders.export');
    Route::get('/profile', [SellerProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [SellerProfileController::class, 'update'])->name('profile.update');
    // ---------------- Public Order Tracking ----------------
Route::get('/tracking/order/{id}', [OrderTrackingController::class, 'show'])->name('tracking.order');
});
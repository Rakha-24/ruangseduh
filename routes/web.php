<?php

use App\Http\Controllers\Admin\CashierController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DiningTableController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\RestaurantProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\PublicReservationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Checkout & Payment (Midtrans)
|--------------------------------------------------------------------------
*/
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/redirect-checkout', [CheckoutController::class, 'callback'])->name('checkout.callback');

// Webhook notification dari server Midtrans — dikecualikan dari CSRF di bootstrap/app.php
Route::post('/payment/notification', [CheckoutController::class, 'notification'])->name('payment.notification');

/*
|--------------------------------------------------------------------------
| Reservasi Meja — Publik (ketersediaan & submit)
|--------------------------------------------------------------------------
*/
Route::get('/reservations/availability', [PublicReservationController::class, 'availability'])->name('reservations.availability');
Route::post('/reservations', [PublicReservationController::class, 'store'])->name('reservations.store');

/*
|--------------------------------------------------------------------------
| Admin Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('admin/login', [AuthenticatedSessionController::class, 'create'])->name('admin.login');
    Route::post('admin/login', [AuthenticatedSessionController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Admin Panel (Protected)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth'])
    ->name('admin.')
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');

        // Content Management — Kategori, Menu, Promo, Galeri, Profil Resto
        Route::resource('categories', CategoryController::class);
        Route::resource('menu', MenuItemController::class);
        Route::resource('promo', PromotionController::class);
        Route::resource('galeri', GalleryController::class)->only(['index', 'store', 'destroy']);

        Route::get('profil', [RestaurantProfileController::class, 'edit'])->name('profil');
        Route::patch('profil', [RestaurantProfileController::class, 'update'])->name('profil.update');

        // Kasir & Antrean
        Route::get('kasir', [CashierController::class, 'index'])->name('kasir');
        Route::post('kasir/order', [CashierController::class, 'store'])->name('kasir.order');
        Route::get('api/orders/active', [CashierController::class, 'fetchActiveOrders'])->name('api.orders.active');

        // Reservasi Meja — meja & manajemen booking
        Route::resource('dining-tables', DiningTableController::class);
        Route::patch('dining-tables/{diningTable}/toggle', [DiningTableController::class, 'toggle'])->name('dining-tables.toggle');
        Route::get('reservations/calendar', [ReservationController::class, 'calendar'])->name('reservations.calendar');
        Route::patch('reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservations.status');
        Route::resource('reservations', ReservationController::class)->only(['index', 'destroy']);
    });

/*
|--------------------------------------------------------------------------
| Breeze fallbacks — legacy "/dashboard" & profile routes
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

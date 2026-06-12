<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\Http\Controllers\CouponController;
use Modules\Cart\Http\Controllers\CartController;
use Modules\Cart\Http\Controllers\WishlistController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Cart routes
    Route::resource('cart', CartController::class)->except(['create', 'edit'])->names('cart');
    Route::get('/dataTable/carts', [CartController::class, 'dataTable'])->name('cart.dataTable');

    // Coupons
    Route::resource('coupons', CouponController::class)->except(['create', 'edit'])->names('coupons');
    Route::get('/dataTable/coupons', [CouponController::class, 'dataTable'])->name('coupons.dataTable');

    // Wishlists
    Route::resource('wishlists', WishlistController::class)->except(['create', 'edit'])->names('wishlists');
    Route::get('/dataTable/wishlists', [WishlistController::class, 'dataTable'])->name('wishlists.dataTable');
});
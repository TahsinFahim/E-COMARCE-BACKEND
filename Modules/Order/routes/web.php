<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\OrderController;
use Modules\Order\Http\Controllers\PaymentController;
use Modules\Order\Http\Controllers\RefundController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Orders
    Route::resource('orders', OrderController::class)->except(['create', 'edit'])->names('orders');
    Route::get('/dataTable/orders', [OrderController::class, 'dataTable'])->name('orders.dataTable');

    // Payments
    Route::resource('payments', PaymentController::class)->except(['create', 'edit'])->names('payments');
    Route::get('/dataTable/payments', [PaymentController::class, 'dataTable'])->name('payments.dataTable');

    // Refunds
    Route::resource('refunds', RefundController::class)->except(['create', 'edit'])->names('refunds');
    Route::get('/dataTable/refunds', [RefundController::class, 'dataTable'])->name('refunds.dataTable');
});
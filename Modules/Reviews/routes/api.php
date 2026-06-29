<?php

use Illuminate\Support\Facades\Route;
use Modules\Reviews\Http\Controllers\ProductReviewApiController;

// Review API routes temporarily disabled due to autoload issue
// Reviews are now integrated into product detail API response
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/products/{productId}/reviews', [ProductReviewApiController::class, 'index'])->name('api.products.reviews.index');
    Route::post('/products/{productId}/reviews', [ProductReviewApiController::class, 'store'])->name('api.products.reviews.store');
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalog\Http\Controllers\BrandController;
use Modules\Catalog\Http\Controllers\CategoryController;
use Modules\Catalog\Http\Controllers\ProductController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('products', ProductController::class)->names('products');
    Route::get('/dataTable/products', [ProductController::class, 'dataTable'])->name('products.dataTable');
    
    Route::resource('brands', BrandController::class)->except(['create', 'edit'])->names('brands');
    Route::get('/dataTable/brands', [BrandController::class, 'dataTable'])->name('brands.dataTable');

    Route::resource('categories', CategoryController::class)->except(['create', 'edit'])->names('categories');
    Route::get('/dataTable/categories', [CategoryController::class, 'dataTable'])->name('categories.dataTable');
});

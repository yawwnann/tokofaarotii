<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\SaleController;


Route::middleware(['auth', 'role:admin_master,kasir'])->name('api.')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('stock-entries', StockEntryController::class);
    Route::apiResource('sales', SaleController::class);
});

Route::post('/midtrans/callback', [\App\Http\Controllers\Webhook\MidtransController::class, 'handle']);

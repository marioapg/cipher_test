<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CurrencyController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('products', [ProductController::class, 'index']);
Route::post('products', [ProductController::class, 'store']);
Route::get('products/{product}', [ProductController::class, 'show']);
Route::put('products/{product}', [ProductController::class, 'update']);
Route::delete('products/{product}', [ProductController::class, 'destroy']);
Route::get('products/{product}/prices', [ProductController::class, 'getPrices']);
Route::post('products/{product}/prices', [ProductController::class, 'createPrice']);
Route::get('currencies', [CurrencyController::class, 'index']);

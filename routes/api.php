<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('products', [ProductController::class, 'index']);
Route::post('products', [ProductController::class, 'store']);

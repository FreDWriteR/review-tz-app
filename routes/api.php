<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddToCartController;
use App\Http\Controllers\GetProductsController;
use App\Http\Controllers\GetCartController;

Route::post('/cart/items',   [AddToCartController::class, 'post']);
Route::get('/products',     [GetProductsController::class, 'get']);
Route::get('/cart', [GetCartController::class, 'get']);

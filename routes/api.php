<?php

use App\Http\Controllers\WindowController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

Route::post('login', [UserController::class, 'login']);

Route::middleware('auth.api')->group(function () {
    Route::resource('users', UserController::class)
        ->except(['create', 'edit']);
    Route::resource('categories', CategoryController::class)
        ->except(['create', 'edit']);
    Route::resource('products', ProductController::class)
        ->except(['create', 'edit']);
    Route::resource('carts', CartController::class)
        ->except(['create', 'edit']);
    Route::resource('orders', OrderController::class)
        ->except(['create', 'edit']);
    Route::get('reports', [OrderController::class, 'report']);
    Route::resource('windows', WindowController::class)
        ->only(['store', 'destroy']);
});
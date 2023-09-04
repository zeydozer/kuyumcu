<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$redirect = config('const.redirect');

Route::get('login', function () use ($redirect) {
    return
        !Session::has('token') ?
        view('login') : 
        redirect($redirect);
});

Route::post('token', function (Request $r) {
    Session::put('token', $r->token);
});

Route::middleware('auth.web')->group(function () use ($redirect) {
    Route::get('logout', function () {
        Session::forget('token');
        return redirect('login');
    });
    Route::redirect('/', $redirect);
    Route::view('orders', 'order.list');
    Route::middleware('auth.admin')->group(function () {
        Route::view('categories', 'category.list');
        Route::view('users', 'user.list');
        Route::view('order-fast/{id}', 'order.fast.update');
        Route::view('order-fast', 'order.fast.new');
    });
    Route::middleware('auth.common')->group(function () {
        Route::view('products', 'product.list');
        Route::view('carts', 'cart.list');
        Route::view('reports', 'order.report');
    });
});

Route::post('mode', function (Request $r) {
    Session::put('mode', $r->type);
});
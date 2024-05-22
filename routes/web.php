<?php

use App\Controllers\AboutController;
use App\Controllers\CategoryController;
use App\Controllers\ContactController;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use app\Controllers\User\CartController;
use app\Controllers\User\DashboardController;
use app\Controllers\User\OrderController;
use App\Core\Routing\RouteProxy as Route;

// Auth Routes
include base_path('routes/auth.php');

// Admin Routes
include base_path('routes/admin.php');


// Home Route
Route::get('/')
    ->controller(HomeController::class)
    ->name('home');

// About Route
Route::get('/about')
    ->controller(AboutController::class)
    ->name('about');

// Contact Routes
Route::get('/contact')
    ->controller([ContactController::class, 'index'])
    ->name('contact.index');
Route::post('/contact')
    ->controller([ContactController::class, 'store'])
    ->name('contact.store');

// Category Routes (show the products of a category)
Route::get('/categories/{category}')
    ->controller([CategoryController::class, 'show'])
    ->name('categories.show');

// Products Routes
Route::get('/products')
    ->controller([ProductController::class, 'index'])
    ->name('products.index');

Route::get('/categories/{category}/products/{product}')
    ->controller([ProductController::class, 'show'])
    ->name('products.show');

// Dashboard Route
Route::get('/dashboard')
    ->controller(DashboardController::class)
    ->name('dashboard.index')
    ->middleware('auth');

// TODO: implement orders feature
// Order Routes
Route::get('/orders')
    ->controller([OrderController::class, 'index'])
    ->name('orders.index')
    ->middleware('auth');

Route::get('/orders/create')
    ->controller([OrderController::class, 'create'])
    ->name('orders.create')
    ->middleware('auth');

Route::post('/orders')
    ->controller([OrderController::class, 'store'])
    ->name('orders.store')
    ->middleware('auth');

Route::get('/orders/{order}')
    ->controller([OrderController::class, 'show'])
    ->name('orders.show')
    ->middleware('auth');

Route::delete('/orders/{order}')
    ->controller([OrderController::class, 'destroy'])
    ->name('orders.destroy')
    ->middleware('auth');

// TODO implement cart feature;
// Cart Routes
Route::get('/cart')
    ->controller([CartController::class, 'show'])
    ->name('cart.show')
    ->middleware('auth');

Route::post('/cart')
    ->controller([CartController::class, 'store'])
    ->name('cart.store')
    ->middleware('auth');

Route::delete('/cart')
    ->controller([CartController::class, 'destroy'])
    ->name('cart.destroy')
    ->middleware('auth');

Route::put('/cart')
    ->controller([CartController::class, 'update'])
    ->name('cart.update')
    ->middleware('auth');

// TODO: Checkout & Order Routes
<?php

use App\Core\Routing\RouteProxy as Route;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\SiteMapGeneratorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\OrderController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\User\DashboardController;

// Auth Routes
include include_path('routes/auth.php');

// Admin Routes
include include_path('routes/admin.php');

// Home Route
Route::get('/')
    ->controller(HomeController::class)
    ->name('home');

Route::get('/robots.txt')
    ->controller(function () {
        return response()->file(public_path('robots.txt'));
})->name('robots');

Route::get('/sitemap.xml')
    ->controller(SiteMapGeneratorController::class)
    ->name('sitemap');

// About Route
Route::get('/about')
    ->controller(AboutController::class)
    ->name('about');

Route::get('/newsletter')
    ->controller([SubscribeController::class, 'index'])
    ->name('subscribe.index');
Route::post('/subscribe')
    ->controller([SubscribeController::class, 'store'])
    ->name('subscribe.store');

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

// Order Routes
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

// Cart Routes
Route::get('/cart')
    ->controller([CartController::class, 'show'])
    ->name('cart.show');

Route::post('/cart')
    ->controller([CartController::class, 'store'])
    ->name('cart.store');

Route::delete('/cart')
    ->controller([CartController::class, 'destroy'])
    ->name('cart.destroy');

Route::put('/cart')
    ->controller([CartController::class, 'update'])
    ->name('cart.update');

// Checkout Routes
Route::get('/checkout')
    ->controller(CheckoutController::class)
    ->name('checkout')
    ->middleware('auth');
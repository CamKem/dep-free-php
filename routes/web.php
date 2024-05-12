<?php

use App\Controllers\AboutController;
use App\Controllers\CategoriesController;
use App\Controllers\ContactController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\ProductsController;
use App\Core\Routing\RouteProxy as Route;

include base_path('routes/auth.php');

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
    ->controller([CategoriesController::class, 'show'])
    ->name('categories.show');

// Products Routes
Route::get('/products')
    ->controller([ProductsController::class, 'index'])
    ->name('products.index');

Route::get('/categories/{category}/products/{product}')
    ->controller([ProductsController::class, 'show'])
    ->name('products.show');

// Dashboard Route
Route::get('/dashboard')
    ->controller(DashboardController::class)
    ->name('dashboard.index')
    ->middleware('auth');
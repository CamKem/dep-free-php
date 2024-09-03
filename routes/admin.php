<?php

use App\Core\Routing\RouteProxy as Route;
use App\HTTP\Controllers\Admin\CategoryController;
use App\HTTP\Controllers\Admin\DashboardController;
use App\HTTP\Controllers\Admin\OrderController;
use App\HTTP\Controllers\Admin\ProductController;
use App\HTTP\Controllers\Admin\RoleController;
use App\HTTP\Controllers\Admin\UserController;

// Roles
Route::get('/admin/roles')
    ->controller([RoleController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.roles.index');

Route::post('/admin/roles')
    ->controller([RoleController::class, 'store'])
    ->middleware(['auth', 'admin'])
    ->name('admin.roles.store');

Route::put('/admin/roles/{id}')
    ->controller([RoleController::class, 'update'])
    ->middleware(['auth', 'admin'])
    ->name('admin.roles.update');

Route::delete('/admin/roles/{id}')
    ->controller([RoleController::class, 'destroy'])
    ->middleware(['auth', 'admin'])
    ->name('admin.roles.destroy');

// Dashboard
Route::get('/admin')
    ->controller(DashboardController::class)
    ->middleware(['auth', 'admin'])
    ->name('admin.index');

// Users
Route::get('/admin/users')
    ->controller([UserController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.users.index');
Route::get('/admin/users/{id}')
    ->controller([UserController::class, 'show'])
    ->middleware(['auth', 'admin'])
    ->name('admin.users.show');
Route::post('/admin/users')
    ->controller([UserController::class, 'store'])
    ->middleware(['auth', 'admin'])
    ->name('admin.users.store');
Route::put('/admin/users/{id}')
    ->controller([UserController::class, 'update'])
    ->middleware(['auth', 'admin'])
    ->name('admin.users.update');
Route::delete('/admin/users/{id}')
    ->controller([UserController::class, 'destroy'])
    ->middleware(['auth', 'admin'])
    ->name('admin.users.destroy');

// Orders
Route::get('/admin/orders')
    ->controller([OrderController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.orders.index');

Route::get('/admin/orders/{id}')
    ->controller([OrderController::class, 'show'])
    ->middleware(['auth', 'admin'])
    ->name('admin.orders.show');

Route::put('/admin/orders/{id}')
    ->controller([OrderController::class, 'update'])
    ->middleware(['auth', 'admin'])
    ->name('admin.orders.update');

Route::delete('/admin/orders/{id}')
    ->controller([OrderController::class, 'destroy'])
    ->middleware(['auth', 'admin'])
    ->name('admin.orders.destroy');

// Categories
Route::get('/admin/categories')
    ->controller([CategoryController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.categories.index');

Route::post('/admin/categories')
    ->controller([CategoryController::class, 'store'])
    ->middleware(['auth', 'admin'])
    ->name('admin.categories.store');

Route::put('/admin/categories/{id}')
    ->controller([CategoryController::class, 'update'])
    ->middleware(['auth', 'admin'])
    ->name('admin.categories.update');

Route::delete('/admin/categories/{id}')
    ->controller([CategoryController::class, 'destroy'])
    ->middleware(['auth', 'admin'])
    ->name('admin.categories.destroy');

// Products
Route::get('/admin/products')
    ->controller([ProductController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.products.index');

Route::post('/admin/products')
    ->controller([ProductController::class, 'store'])
    ->middleware(['auth', 'admin'])
    ->name('admin.products.store');

// image upload route
Route::post('/admin/products/image')
    ->controller([ProductController::class, 'imageUpload'])
    ->middleware(['auth', 'admin'])
    ->name('admin.products.image');

Route::put('/admin/products/{id}')
    ->controller([ProductController::class, 'update'])
    ->middleware(['auth', 'admin'])
    ->name('admin.products.update');

Route::delete('/admin/products/{id}')
    ->controller([ProductController::class, 'destroy'])
    ->middleware(['auth', 'admin'])
    ->name('admin.products.destroy');
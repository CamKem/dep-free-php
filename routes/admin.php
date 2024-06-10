<?php

use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\OrderController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\UserController;
use App\Core\Routing\RouteProxy as Route;


#// DONt save credit card number... put dummy data.
#// ADMIN and password username and password
#// DATABASE name sportswh.sql
#// Upload of the category file.

#// Upload files for new products.

// NOTE: implement pagination for the products and users

// TODO: Add the routes for the admin panel

// TODO: add the checkout and order routes for the user

// Add an admin layout, that is similar to the user layout, but has the links where the categories are in user layout


// Admin must be able to reset password directly from the admin panel

// Admin must be able to view all users,
// Admin must be able to update, delete users, trigger password reset
// and assign roles to users

// Admin must be able to view all products
// Admin must be able to create, update, delete products

// Admin must be able to view all orders
// Admin must be able to update the status of an order

// Admin must be able to view all categories
// create, update, delete

// Admin must be able to view all roles
// create, update, delete


// Dashboard
Route::get('/admin')
    ->controller(DashboardController::class)
    ->middleware('admin')
    ->name('admin.index');

// Users
Route::get('/admin/users')
    ->controller([UserController::class, 'index'])
    ->middleware('admin')
    ->name('admin.users.index');
Route::get('/admin/users/{id}')
    ->controller([UserController::class, 'show'])
    ->middleware('admin')
    ->name('admin.users.show');
Route::get('/admin/users/create')
    ->controller([UserController::class, 'create'])
    ->middleware('admin')
    ->name('admin.users.create');
Route::post('/admin/users')
    ->controller([UserController::class, 'store'])
    ->middleware('admin')
    ->name('admin.users.store');
Route::get('/admin/users/{id}/edit')
    ->controller([UserController::class, 'edit'])
    ->middleware('admin')
    ->name('admin.users.edit');
Route::put('/admin/users/{id}')
    ->controller([UserController::class, 'update'])
    ->middleware('admin')
    ->name('admin.users.update');
Route::delete('/admin/users/{id}')
    ->controller([UserController::class, 'destroy'])
    ->middleware('admin')
    ->name('admin.users.destroy');

// Products
Route::get('/admin/products')
    ->controller([ProductController::class, 'index'])
    ->middleware('admin')
    ->name('admin.products.index');
Route::get('/admin/products/{id}')
    ->controller([ProductController::class, 'show'])
    ->middleware('admin')
    ->name('admin.products.show');
Route::get('/admin/products/create')
    ->controller([ProductController::class, 'create'])
    ->middleware('admin')
    ->name('admin.products.create');
Route::post('/admin/products')
    ->controller([ProductController::class, 'store'])
    ->middleware('admin')
    ->name('admin.products.store');
Route::get('/admin/products/{id}/edit')
    ->controller([ProductController::class, 'edit'])
    ->middleware('admin')
    ->name('admin.products.edit');
Route::put('/admin/products/{id}')
    ->controller([ProductController::class, 'update'])
    ->middleware('admin')
    ->name('admin.products.update');
Route::delete('/admin/products/{id}')
    ->controller([ProductController::class, 'destroy'])
    ->middleware('admin')
    ->name('admin.products.destroy');

// Orders
Route::get('/admin/orders')
    ->controller([OrderController::class, 'index'])
    ->middleware('admin')
    ->name('admin.orders.index');

Route::get('/admin/orders/{id}')
    ->controller([OrderController::class, 'show'])
    ->middleware('admin')
    ->name('admin.orders.show');

Route::get('/admin/orders/{id}/edit')
    ->controller([OrderController::class, 'edit'])
    ->middleware('admin')
    ->name('admin.orders.edit');

Route::put('/admin/orders/{id}')
    ->controller([OrderController::class, 'update'])
    ->middleware('admin')
    ->name('admin.orders.update');

Route::delete('/admin/orders/{id}')
    ->controller([OrderController::class, 'destroy'])
    ->middleware('admin')
    ->name('admin.orders.destroy');

// Categories

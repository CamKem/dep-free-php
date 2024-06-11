<?php

use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\OrderController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\ProfileController;
use App\Controllers\Admin\RoleController;
use App\Controllers\Admin\SettingsController;
use App\Controllers\Admin\UserController;
use App\Core\Routing\RouteProxy as Route;


#// DONt save credit card number... put dummy data.
#// ADMIN and password username and password
#// DATABASE name sportswh.sql

#// Upload photo files for new products.

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
Route::get('/admin/users/{id}/edit')
    ->controller([UserController::class, 'edit'])
    ->middleware(['auth', 'admin'])
    ->name('admin.users.edit');
Route::put('/admin/users/{id}')
    ->controller([UserController::class, 'update'])
    ->middleware(['auth', 'admin'])
    ->name('admin.users.update');
Route::delete('/admin/users/{id}')
    ->controller([UserController::class, 'destroy'])
    ->middleware(['auth', 'admin'])
    ->name('admin.users.destroy');

// Products
Route::get('/admin/products')
    ->controller([ProductController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.products.index');
Route::get('/admin/products/{id}')
    ->controller([ProductController::class, 'show'])
    ->middleware(['auth', 'admin'])
    ->name('admin.products.show');
Route::get('/admin/products/create')
    ->controller([ProductController::class, 'create'])
    ->middleware(['auth', 'admin'])
    ->name('admin.products.create');
Route::post('/admin/products')
    ->controller([ProductController::class, 'store'])
    ->middleware(['auth', 'admin'])
    ->name('admin.products.store');
Route::get('/admin/products/{id}/edit')
    ->controller([ProductController::class, 'edit'])
    ->middleware(['auth', 'admin'])
    ->name('admin.products.edit');
Route::put('/admin/products/{id}')
    ->controller([ProductController::class, 'update'])
    ->middleware(['auth', 'admin'])
    ->name('admin.products.update');
Route::delete('/admin/products/{id}')
    ->controller([ProductController::class, 'destroy'])
    ->middleware(['auth', 'admin'])
    ->name('admin.products.destroy');

// Orders
Route::get('/admin/orders')
    ->controller([OrderController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.orders.index');

Route::get('/admin/orders/{id}')
    ->controller([OrderController::class, 'show'])
    ->middleware(['auth', 'admin'])
    ->name('admin.orders.show');

Route::get('/admin/orders/{id}/edit')
    ->controller([OrderController::class, 'edit'])
    ->middleware(['auth', 'admin'])
    ->name('admin.orders.edit');

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

Route::get('/admin/categories/create')
    ->controller([CategoryController::class, 'create'])
    ->middleware(['auth', 'admin'])
    ->name('admin.categories.create');

Route::post('/admin/categories')
    ->controller([CategoryController::class, 'store'])
    ->middleware(['auth', 'admin'])
    ->name('admin.categories.store');

Route::get('/admin/categories/{id}/edit')
    ->controller([CategoryController::class, 'edit'])
    ->middleware(['auth', 'admin'])
    ->name('admin.categories.edit');

Route::put('/admin/categories/{id}')
    ->controller([CategoryController::class, 'update'])
    ->middleware(['auth', 'admin'])
    ->name('admin.categories.update');

Route::delete('/admin/categories/{id}')
    ->controller([CategoryController::class, 'destroy'])
    ->middleware(['auth', 'admin'])
    ->name('admin.categories.destroy');

// Setting - add roles, update password, change profile
Route::get('/admin/settings')
    ->controller(SettingsController::class)
    ->middleware(['auth', 'admin'])
    ->name('admin.settings.index');

// Role Settings
//The table will show all the details needed for the roles so we don't need a show route
Route::get('/admin/settings/roles')
    ->controller([RoleController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.roles.index');

Route::get('/admin/settings/roles/create')
    ->controller([RoleController::class, 'create'])
    ->middleware(['auth', 'admin'])
    ->name('admin.roles.create');

Route::post('/admin/settings/roles')
    ->controller([RoleController::class, 'store'])
    ->middleware(['auth', 'admin'])
    ->name('admin.roles.store');

Route::get('/admin/settings/roles/{id}/edit')
    ->controller([RoleController::class, 'edit'])
    ->middleware(['auth', 'admin'])
    ->name('admin.roles.edit');

Route::put('/admin/settings/roles/{id}')
    ->controller([RoleController::class, 'update'])
    ->middleware(['auth', 'admin'])
    ->name('admin.roles.update');

Route::delete('/admin/settings/roles/{id}')
    ->controller([RoleController::class, 'destroy'])
    ->middleware(['auth', 'admin'])
    ->name('admin.roles.destroy');

// Profile Settings
Route::get('/admin/settings/profile')
    ->controller([ProfileController::class, 'show'])
    ->middleware(['auth', 'admin'])
    ->name('admin.profile.show');

Route::get('/admin/settings/profile/edit')
    ->controller([ProfileController::class, 'edit'])
    ->middleware(['auth', 'admin'])
    ->name('admin.profile.edit');

Route::put('/admin/settings/profile')
    ->controller([ProfileController::class, 'update'])
    ->middleware(['auth', 'admin'])
    ->name('admin.profile.update');
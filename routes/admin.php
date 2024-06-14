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

#// DATABASE name sportswh.sql
#// allow login with username (for admin only) and email (for users)

// NOTE: Fix the menu toggle on safari, it's not working local & production
// Admin must be able to view all roles
// create, update, delete

// NOTE: when it's finished, all routes need to be tested for each different edge case

// Setting - add roles, update password, change profile

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

########## NOTE: up to here::

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
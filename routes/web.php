<?php

use App\Controllers\AboutController;
use App\Controllers\Auth\SessionController;
use App\Controllers\CategoriesController;
use App\Controllers\ContactController;
use App\Controllers\HomeController;
use App\Controllers\NotesController;
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

// Login routes
Route::get('/login')
    ->controller([SessionController::class, 'index'])
    ->name('login.index');

// Notes Routes
Route::get('/notes')
    //->middleware('auth')
    ->controller([NotesController::class, 'index'])
    ->name('notes.index');

Route::get('/notes/create')
    //->middleware('auth')
    ->controller([NotesController::class, 'create'])
    ->name('notes.create');

Route::get('/notes/{note}')
    //->middleware('auth')
    ->controller([NotesController::class, 'show'])
    ->name('notes.show');

Route::delete('/notes/{note}')
    //->middleware('auth')
    ->controller([NotesController::class, 'destroy'])
    ->name('notes.destroy');

Route::post('/notes')
    //->middleware('auth')
    ->controller([NotesController::class, 'store'])
    ->name('notes.store');

Route::get('/notes/{note}/edit')
    //->middleware('auth')
    ->controller([NotesController::class, 'edit'])
    ->name('notes.edit');

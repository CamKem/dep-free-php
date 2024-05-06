<?php

use App\Core\Routing\RouteProxy as Route;
use App\Controllers\AboutController;
use App\Controllers\CategoriesController;
use App\Controllers\ContactController;
use App\Controllers\HomeController;
use App\Controllers\NotesController;

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

// Categories Routes
Route::get('/categories')
    ->controller([CategoriesController::class, 'index'])
    ->name('categories.index');

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

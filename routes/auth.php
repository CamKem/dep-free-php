<?php

use App\Controllers\Auth\PasswordResetController;
use App\Controllers\Auth\RegistrationController;
use App\Controllers\Auth\SessionController;
use App\Core\Routing\RouteProxy as Route;

// Login routes
Route::get('/login')
    ->controller([SessionController::class, 'index'])
    ->name('login.index');

Route::post('/login')
    ->controller([SessionController::class, 'store'])
    ->name('login.store');

Route::get('/logout')
    ->controller([SessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Registration routes
Route::get('/register')
    ->controller([RegistrationController::class, 'index'])
    ->name('register.index');

Route::post('/register')
    ->controller([RegistrationController::class, 'store'])
    ->name('register.store');

// Password reset routes
Route::get('/password/reset')
    ->controller([PasswordResetController::class, 'index'])
    ->name('password.reset.index');

Route::post('/password/reset')
    ->controller([PasswordResetController::class, 'store'])
    ->name('password.reset.store');

Route::get('/password/reset/{token}')
    ->controller([PasswordResetController::class, 'edit'])
    ->name('password.reset.edit');
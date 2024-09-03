<?php

use App\Core\Routing\RouteProxy as Route;
use App\HTTP\Controllers\Auth\PasswordResetController;
use App\HTTP\Controllers\Auth\RegistrationController;
use App\HTTP\Controllers\Auth\SessionController;

// Login routes
Route::get('/login')
    ->controller([SessionController::class, 'index'])
    ->middleware('guest')
    ->name('login.index');

Route::post('/login')
    ->controller([SessionController::class, 'store'])
    ->middleware('guest')
    ->name('login.store');

Route::get('/logout')
    ->controller([SessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Registration routes
Route::get('/register')
    ->controller([RegistrationController::class, 'index'])
    ->middleware('guest')
    ->name('register.index');

Route::post('/register')
    ->controller([RegistrationController::class, 'store'])
    ->middleware('guest')
    ->name('register.store');

// Password reset routes
Route::get('/password/reset')
    ->controller([PasswordResetController::class, 'show'])
    ->name('password.reset.show');

Route::post('/password/reset')
    ->controller([PasswordResetController::class, 'store'])
    ->name('password.reset.store');

Route::get('/password/reset/{token}')
    ->controller([PasswordResetController::class, 'edit'])
    ->name('password.reset.edit');

Route::post('/password/reset/{token}')
    ->controller([PasswordResetController::class, 'update'])
    ->name('password.reset.update');
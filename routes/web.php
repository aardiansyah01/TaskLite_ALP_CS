<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuthController;

Route::view('/', 'login')->name('login.page');

Route::view('/dashboard', 'dashboard')
    ->name('dashboard.page');

Route::get(
    '/auth/google/redirect',
    [GoogleAuthController::class, 'redirectToGoogle']
)->name('google.redirect');

Route::get(
    '/auth/google/callback',
    [GoogleAuthController::class, 'handleGoogleCallback']
)->name('google.callback');
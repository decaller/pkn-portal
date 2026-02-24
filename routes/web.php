<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get(
    '/login',
    fn () => redirect()->route('filament.user.auth.login'),
)->name('login');
Route::get(
    '/register',
    fn () => redirect()->route('filament.user.auth.register'),
)->name('register');
Route::get(
    '/registerevent',
    fn () => redirect()->route('filament.user.auth.registerevent'),
)->name('registerevent');

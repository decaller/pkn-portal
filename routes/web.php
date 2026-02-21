<?php

use Illuminate\Support\Facades\Route;

Route::get("/", fn() => redirect()->route("filament.user.pages.dashboard"));

Route::get(
    "/login",
    fn() => redirect()->route("filament.user.auth.login"),
)->name("login");
Route::get(
    "/register",
    fn() => redirect()->route("filament.user.auth.register"),
);

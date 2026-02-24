<?php

use App\Models\Invoice;
use App\Services\InvoicePdfService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;

Route::view("/", "home")->name("home");

Route::get(
    "/login",
    fn() => redirect()->route("filament.user.auth.login"),
)->name("login");
Route::get(
    "/register",
    fn() => redirect()->route("filament.user.auth.register"),
)->name("register");

Route::middleware("auth")
    ->get("/invoices/{invoice}/download", function (
        Invoice $invoice,
        InvoicePdfService $service,
    ) {
        $invoice->loadMissing("registration");
        Gate::authorize("view", $invoice->registration);

        return $service->download($invoice);
    })
    ->name("invoices.download");

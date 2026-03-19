<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\NewsController;
use App\Http\Controllers\Api\V1\WebViewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // 1. Public Core (No-Login MVP)
    Route::get('/mobile-dashboard', [DashboardController::class, 'index']);

    Route::controller(EventController::class)->group(function () {
        Route::get('/events', 'index');
        Route::get('/events/{event}', 'show');
        Route::get('/events/{event}/similar', 'similar');
    });

    Route::controller(NewsController::class)->group(function () {
        Route::get('/news', 'index');
        Route::get('/news/{news}', 'show');
    });

    Route::controller(DocumentController::class)->group(function () {
        Route::get('/documents', 'index');
        Route::get('/documents/{document}', 'show');
    });

    // 2. Hybrid Login & Identity
    Route::get('/auth/token-handoff', [AuthController::class, 'tokenHandoff'])->middleware('web');

    // 3. Authenticated Screens
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::get('/webview/magic-link', [WebViewController::class, 'getMagicLink']);

        // Profile, Notifications, Registrations, Invoices (Phase 2-5) will go here
    });
});

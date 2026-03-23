<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\NewsController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OrganizationController;
use App\Http\Controllers\Api\V1\ParticipantController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\RegistrationController;
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

    // 2. Native Login & Identity
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->name('api.v1.auth.login');

    Route::get('/auth/token-handoff', [AuthController::class, 'tokenHandoff'])
        ->middleware('web')
        ->name('api.v1.auth.token-handoff');

    // 3. Authenticated Screens
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Profile
        Route::get('/user/profile', [ProfileController::class, 'show']);
        Route::put('/user/profile', [ProfileController::class, 'update']);
        Route::delete('/user/profile', [ProfileController::class, 'destroy']);

        // Organizations
        Route::apiResource('organizations', OrganizationController::class);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);
        Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markRead']);

        // Registrations
        Route::apiResource('registrations', RegistrationController::class);

        // Participants
        Route::get('/registrations/{registration_id}/participants', [ParticipantController::class, 'index']);
        Route::post('/registrations/{registration_id}/participants', [ParticipantController::class, 'store']);
        Route::put('/participants/{participant}', [ParticipantController::class, 'update']);
        Route::delete('/participants/{participant}', [ParticipantController::class, 'destroy']);

        // Invoices
        Route::get('/invoices', [InvoiceController::class, 'index']);
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);
        Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download']);
    });
});

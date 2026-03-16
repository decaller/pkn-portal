<?php

use Illuminate\Support\Facades\Route;

beforeEach(function () {
    // Define a dummy route that throws 403 for testing redirect
    Route::get('/test-403', function () {
        abort(403);
    })->middleware('web');

    // Define a dummy route to test security headers
    Route::get('/test-headers', function () {
        return response('ok');
    })->middleware('web');
});

test('it redirects 403 to user login page', function () {
    $response = $this->get('/test-403');

    $response->assertRedirect(route('filament.user.auth.login'));
});

test('it includes security headers in response', function () {
    $response = $this->get('/test-headers');

    $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
});

<?php

test('the application homepage returns a successful response', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee(route('filament.user.auth.login'));
    $response->assertSee(route('filament.user.auth.register'));
    $response->assertSee(route('filament.admin.auth.login'));
});

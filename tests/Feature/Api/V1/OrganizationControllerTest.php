<?php

use App\Models\User;
use App\Models\Organization;
use Laravel\Sanctum\Sanctum;

test('authenticated user can list their organizations', function () {
    $user = User::factory()->has(Organization::factory()->count(3))->create();
    Sanctum::actingAs($user, ['*']);

    $response = $this->getJson('/api/v1/organizations');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('unauthenticated user cannot list organizations', function () {
    $response = $this->getJson('/api/v1/organizations');
    
    $response->assertStatus(401);
});

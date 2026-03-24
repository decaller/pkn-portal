<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

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

test('authenticated user can create organization', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $response = $this->postJson('/api/v1/organizations', [
        'name' => 'My New Organization',
        'logo' => 'https://example.com/logo.png',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.name', 'My New Organization');

    $this->assertDatabaseHas('organizations', [
        'name' => 'My New Organization',
        'admin_user_id' => $user->id,
    ]);
});

test('authenticated user can update their organization', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->create(['admin_user_id' => $user->id]);
    Sanctum::actingAs($user, ['*']);

    $response = $this->putJson("/api/v1/organizations/{$organization->id}", [
        'name' => 'Updated Name',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.name', 'Updated Name');

    $this->assertDatabaseHas('organizations', [
        'id' => $organization->id,
        'name' => 'Updated Name',
    ]);
});

test('user cannot update organization they do not own', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $organization = Organization::factory()->create(['admin_user_id' => $otherUser->id]);
    Sanctum::actingAs($user, ['*']);

    $response = $this->putJson("/api/v1/organizations/{$organization->id}", [
        'name' => 'Hacker Name',
    ]);

    $response->assertStatus(403);
});

test('authenticated user can delete their organization', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->create(['admin_user_id' => $user->id]);
    Sanctum::actingAs($user, ['*']);

    $response = $this->deleteJson("/api/v1/organizations/{$organization->id}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('organizations', [
        'id' => $organization->id,
    ]);
});

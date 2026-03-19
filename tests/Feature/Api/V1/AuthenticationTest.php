<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can handoff web session for sanctum token', function () {
    $user = User::factory()->create();

    // Simulate being logged in via web (session)
    $response = $this->actingAs($user, 'web')
        ->getJson('/api/v1/auth/token-handoff');

    $response->assertStatus(200)
        ->assertJsonStructure(['success', 'token', 'user'])
        ->assertJsonPath('success', true);

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'name' => 'mobile-app',
    ]);
});

test('user can fetch their profile via sanctum', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson('/api/v1/auth/me');

    $response->assertStatus(200)
        ->assertJsonPath('data.email', $user->email);
});

test('user can logout via sanctum', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/auth/logout');

    $response->assertStatus(200);

    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $user->id,
    ]);
});

<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('can login natively with valid credentials', function () {
    $user = User::factory()->create([
        'phone_number' => '08123456789',
        'password' => bcrypt('password123'),
    ]);

    $response = postJson('/api/v1/auth/login', [
        'phone_number' => '08123456789',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['success', 'token', 'user']);
});

it('fails to login with invalid credentials', function () {
    $user = User::factory()->create([
        'phone_number' => '08123456789',
        'password' => bcrypt('password123'),
    ]);

    $response = postJson('/api/v1/auth/login', [
        'phone_number' => '08123456789',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401);
});

it('can fetch authenticated user profile', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user, ['*']);

    $response = getJson('/api/v1/auth/me');

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $user->id);
});

it('can logout and revoke tokens', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $response = postJson('/api/v1/auth/logout');

    $response->assertStatus(200)
        ->assertJson(['success' => true]);
});

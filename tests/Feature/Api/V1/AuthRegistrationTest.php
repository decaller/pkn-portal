<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('can register a new user', function () {
    $response = postJson('/api/v1/auth/register', [
        'name' => 'Test User',
        'phone_number' => '08123456789',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['success', 'token', 'user'])
        ->assertJsonPath('user.name', 'Test User')
        ->assertJsonPath('user.phone_number', '08123456789');

    $this->assertDatabaseHas('users', [
        'phone_number' => '08123456789',
    ]);
});

it('fails to register with existing phone number', function () {
    User::factory()->create(['phone_number' => '08123456789']);

    $response = postJson('/api/v1/auth/register', [
        'name' => 'Test User 2',
        'phone_number' => '08123456789',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['phone_number']);
});

it('fails to register if password confirmation mismatch', function () {
    $response = postJson('/api/v1/auth/register', [
        'name' => 'Test User',
        'phone_number' => '08123456789',
        'password' => 'password123',
        'password_confirmation' => 'wrongpassword',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

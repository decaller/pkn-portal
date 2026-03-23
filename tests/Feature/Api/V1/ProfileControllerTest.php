<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

test('authenticated user can fetch their profile with organizations', function () {
    $user = User::factory()->has(Organization::factory()->count(2))->create();
    Sanctum::actingAs($user, ['*']);

    $response = $this->getJson('/api/v1/user/profile');

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.name', $user->name)
        ->assertJsonCount(2, 'data.organizations');
});

test('unauthenticated user cannot fetch profile', function () {
    $response = $this->getJson('/api/v1/user/profile');

    $response->assertStatus(401);
});

test('authenticated user can update their profile', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $email = Str::random(10).'@example.com';
    $phone = (string) rand(1000000000, 9999999999);

    $response = $this->putJson('/api/v1/user/profile', [
        'name' => 'Updated Name',
        'email' => $email,
        'phone_number' => $phone,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.name', 'Updated Name')
        ->assertJsonPath('data.email', $email);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => $email,
        'phone_number' => $phone,
    ]);
});

test('profile update validation fails on invalid data', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $response = $this->putJson('/api/v1/user/profile', [
        'name' => '', // Required
        'email' => 'not-an-email', // Must be email
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email']);
});

test('authenticated user can delete their profile', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $response = $this->deleteJson('/api/v1/user/profile');

    $response->assertStatus(204);

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

test('unauthenticated user cannot delete profile', function () {
    $response = $this->deleteJson('/api/v1/user/profile');

    $response->assertStatus(401);
});

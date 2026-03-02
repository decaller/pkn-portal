<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->superAdmin = User::factory()->create([
        'is_super_admin' => true,
    ]);

    $this->mainAdmin = User::factory()->create([
        'is_super_admin' => false,
    ]);

    $this->normalUser = User::factory()->create([
        'is_super_admin' => false,
    ]);

    $this->organization = Organization::create([
        'name' => 'Test Organization',
        'slug' => 'org-'.Str::lower(Str::random(8)),
        'admin_user_id' => $this->mainAdmin->getKey(),
    ]);

    $this->organization->users()->syncWithoutDetaching([
        $this->superAdmin->getKey() => ['role' => 'admin'],
        $this->mainAdmin->getKey() => ['role' => 'admin'],
        $this->normalUser->getKey() => ['role' => 'member'],
    ]);
});

dataset('api-auth-users', [
    'super admin' => 'superAdmin',
    'main admin' => 'mainAdmin',
    'normal user' => 'normalUser',
]);

it('allows super admin, main admin, and normal user to login and receive token', function (string $userKey): void {
    $user = $this->{$userKey};

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Login success.');

    expect($response->json('token'))
        ->toBeString()
        ->not->toBeEmpty();

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_type' => User::class,
        'tokenable_id' => $user->getKey(),
    ]);
})->with('api-auth-users');

it('rejects login with invalid credentials for public request', function (): void {
    $this->postJson('/api/auth/login', [
        'email' => $this->normalUser->email,
        'password' => 'wrong-password',
    ])
        ->assertUnauthorized()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'The provided credentials are incorrect.');
});

it('allows super admin, main admin, and normal user to logout with bearer token', function (string $userKey): void {
    $user = $this->{$userKey};
    $token = $user->createToken('test-token')->plainTextToken;

    $this->postJson('/api/auth/logout', [], [
        'Authorization' => 'Bearer '.$token,
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Logout success.');

    expect($user->tokens()->count())->toBe(0);
})->with('api-auth-users');

it('rejects logout for public unauthenticated request', function (): void {
    $this->postJson('/api/auth/logout')
        ->assertUnauthorized();
});

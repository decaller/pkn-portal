<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('authenticated user can list their notifications', function () {
    $user = User::factory()->create();

    // Create notifications manually to avoid missing factory
    for ($i = 0; $i < 3; $i++) {
        DatabaseNotification::insert([
            'id' => Str::orderedUuid(),
            'type' => 'App\Notifications\ExampleNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => json_encode(['message' => 'Test Notification']),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    Sanctum::actingAs($user, ['*']);

    $response = $this->getJson('/api/v1/notifications');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('authenticated user can mark a notification as read', function () {
    $user = User::factory()->create();
    $notificationId = Str::orderedUuid();

    DatabaseNotification::insert([
        'id' => $notificationId,
        'type' => 'App\Notifications\ExampleNotification',
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
        'data' => json_encode(['message' => 'Test Notification']),
        'read_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Sanctum::actingAs($user, ['*']);

    $response = $this->postJson("/api/v1/notifications/{$notificationId}/mark-read");

    $response->assertStatus(200)
        ->assertJson(['message' => 'Notification marked as read.']);

    expect(DatabaseNotification::find($notificationId)->read_at)->not->toBeNull();
});

test('authenticated user can mark all notifications as read', function () {
    $user = User::factory()->create();

    for ($i = 0; $i < 3; $i++) {
        DatabaseNotification::insert([
            'id' => Str::orderedUuid(),
            'type' => 'App\Notifications\ExampleNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => json_encode(['message' => 'Test Notification']),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    Sanctum::actingAs($user, ['*']);

    $response = $this->postJson('/api/v1/notifications/mark-all-read');

    $response->assertStatus(200)
        ->assertJson(['message' => 'All notifications marked as read.']);

    expect($user->unreadNotifications()->count())->toBe(0);
});

test('authenticated user can get unread notification count', function () {
    $user = User::factory()->create();

    DatabaseNotification::insert([
        'id' => Str::orderedUuid(),
        'type' => 'App\Notifications\ExampleNotification',
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
        'data' => json_encode(['message' => 'Test Notification']),
        'read_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Sanctum::actingAs($user, ['*']);

    $response = $this->getJson('/api/v1/notifications/unread-count');

    $response->assertStatus(200)
        ->assertJson(['unread_count' => 1]);
});

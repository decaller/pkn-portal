<?php

use App\Models\Event;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('can create a native registration', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create([
        'allow_registration' => true,
        'event_date' => now()->addDays(10),
        'max_capacity' => 100,
    ]);

    $response = actingAs($user, 'sanctum')->postJson('/api/v1/registrations', [
        'event_id' => $event->id,
        'participants' => [
            ['name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '1234567890'],
        ],
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.event_id', $event->id);

    $this->assertDatabaseHas('event_registrations', [
        'event_id' => $event->id,
        'booker_user_id' => $user->id,
    ]);
});

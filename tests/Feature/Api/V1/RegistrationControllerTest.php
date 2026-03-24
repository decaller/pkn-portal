<?php

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('authenticated user can create registration', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create([
        'allow_registration' => true,
        'max_capacity' => 100,
        'registration_packages' => [
            [
                'id' => 'regular',
                'name' => 'Regular',
                'price' => 1000,
            ],
        ],
    ]);
    $org = Organization::factory()->create();

    Sanctum::actingAs($user, ['*']);

    $response = $this->postJson('/api/v1/registrations', [
        'event_id' => $event->id,
        'organization_id' => $org->id,
        'package_id' => 'regular',
        'participants' => [
            ['name' => 'John Doe', 'email' => 'john@example.com'],
        ],
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.event_id', $event->id);

    $this->assertDatabaseHas('event_registrations', [
        'event_id' => $event->id,
        'booker_user_id' => $user->id,
        'organization_id' => $org->id,
    ]);

    $this->assertDatabaseHas('registration_participants', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

test('user cannot register for event not allowing registration', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create(['allow_registration' => false]);

    Sanctum::actingAs($user, ['*']);

    $response = $this->postJson('/api/v1/registrations', [
        'event_id' => $event->id,
        'participants' => [
            ['name' => 'John Doe'],
        ],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['event_id']);
});

test('authenticated user can update their draft registration', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();

    $registration = EventRegistration::create([
        'booker_user_id' => $user->id,
        'event_id' => $event->id,
        'organization_id' => $org1->id,
        'payment_status' => PaymentStatus::Unpaid,
        'status' => RegistrationStatus::Draft,
        'total_amount' => 1000,
    ]);

    Sanctum::actingAs($user, ['*']);

    $response = $this->putJson("/api/v1/registrations/{$registration->id}", [
        'organization_id' => $org2->id,
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('event_registrations', [
        'id' => $registration->id,
        'organization_id' => $org2->id,
    ]);
});

test('cannot update paid registration', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();

    $registration = EventRegistration::create([
        'booker_user_id' => $user->id,
        'event_id' => $event->id,
        'payment_status' => PaymentStatus::Verified,
        'status' => RegistrationStatus::Draft,
        'total_amount' => 1000,
    ]);

    Sanctum::actingAs($user, ['*']);

    $response = $this->putJson("/api/v1/registrations/{$registration->id}", [
        'organization_id' => 1,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['registration']);
});

test('authenticated user can delete draft registration', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();

    $registration = EventRegistration::create([
        'booker_user_id' => $user->id,
        'event_id' => $event->id,
        'payment_status' => PaymentStatus::Unpaid,
        'status' => RegistrationStatus::Draft,
        'total_amount' => 1000,
    ]);

    Sanctum::actingAs($user, ['*']);

    $response = $this->deleteJson("/api/v1/registrations/{$registration->id}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('event_registrations', [
        'id' => $registration->id,
    ]);
});

test('authenticated user can list their registrations', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();

    EventRegistration::create([
        'booker_user_id' => $user->id,
        'event_id' => $event->id,
        'payment_status' => PaymentStatus::Unpaid,
        'status' => RegistrationStatus::Draft,
        'total_amount' => 1000,
    ]);

    Sanctum::actingAs($user, ['*']);

    $response = $this->getJson('/api/v1/registrations');

    $response->assertStatus(200);
    assertMatchesApiResult($response, 'registrations.json');
});

test('authenticated user can view specific registration', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();

    $registration = EventRegistration::create([
        'booker_user_id' => $user->id,
        'event_id' => $event->id,
        'payment_status' => PaymentStatus::Unpaid,
        'status' => RegistrationStatus::Draft,
        'total_amount' => 1000,
    ]);

    Sanctum::actingAs($user, ['*']);

    $response = $this->getJson("/api/v1/registrations/{$registration->id}");

    $response->assertStatus(200);
    assertMatchesApiResult($response, 'registrations/9.json');
});

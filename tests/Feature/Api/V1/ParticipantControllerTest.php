<?php

use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\RegistrationParticipant;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use Laravel\Sanctum\Sanctum;

test('authenticated user can list participants of their registration', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    
    $registration = EventRegistration::create([
        'booker_user_id' => $user->id,
        'event_id' => $event->id,
        'payment_status' => PaymentStatus::Unpaid,
        'status' => RegistrationStatus::Draft,
        'total_amount' => 1000
    ]);
    
    RegistrationParticipant::create(['registration_id' => $registration->id, 'name' => 'A', 'email' => 'a@a.com', 'user_id' => $user->id]);
    RegistrationParticipant::create(['registration_id' => $registration->id, 'name' => 'B', 'email' => 'a@b.com', 'user_id' => \App\Models\User::factory()->create()->id]);

    Sanctum::actingAs($user, ['*']);

    $response = $this->getJson("/api/v1/registrations/{$registration->id}/participants");

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('user cannot add participant to paid registration', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create([
        'allow_registration' => true,
    ]);
    
    $registration = EventRegistration::create([
        'booker_user_id' => $user->id,
        'event_id' => $event->id,
        'payment_status' => PaymentStatus::Verified,
        'status' => RegistrationStatus::Draft,
        'total_amount' => 1000
    ]);

    Sanctum::actingAs($user, ['*']);

    $response = $this->postJson("/api/v1/registrations/{$registration->id}/participants", [
        'name' => 'New Guy'
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['participant']);
});

test('user can add participant to draft registration', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create([
         'allow_registration' => true,
    ]);
    
    $registration = EventRegistration::create([
        'booker_user_id' => $user->id,
        'event_id' => $event->id,
        'payment_status' => PaymentStatus::Unpaid,
        'status' => RegistrationStatus::Draft,
        'total_amount' => 1000
    ]);

    Sanctum::actingAs($user, ['*']);

    $response = $this->postJson("/api/v1/registrations/{$registration->id}/participants", [
        'name' => 'New Guy',
        'email' => 'newguy@example.com'
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.name', 'New Guy');
        
    $this->assertDatabaseHas('registration_participants', [
        'registration_id' => $registration->id,
        'name' => 'New Guy'
    ]);
});

test('user can update participant', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    
    $registration = EventRegistration::create([
        'booker_user_id' => $user->id,
        'event_id' => $event->id,
        'payment_status' => PaymentStatus::Unpaid,
        'status' => RegistrationStatus::Draft,
        'total_amount' => 1000
    ]);
    
    $participant = RegistrationParticipant::create([
        'registration_id' => $registration->id,
        'name' => 'Old Name',
        'user_id' => $user->id
    ]);

    Sanctum::actingAs($user, ['*']);

    $response = $this->putJson("/api/v1/participants/{$participant->id}", [
        'name' => 'Updated Name'
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('registration_participants', [
        'id' => $participant->id,
        'name' => 'Updated Name'
    ]);
});

test('user can delete participant', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    
    $registration = EventRegistration::create([
        'booker_user_id' => $user->id,
        'event_id' => $event->id,
        'payment_status' => PaymentStatus::Unpaid,
        'status' => RegistrationStatus::Draft,
        'total_amount' => 1000
    ]);
    
    $participant = RegistrationParticipant::create([
        'registration_id' => $registration->id,
        'user_id' => $user->id
    ]);

    Sanctum::actingAs($user, ['*']);

    $response = $this->deleteJson("/api/v1/participants/{$participant->id}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('registration_participants', [
        'id' => $participant->id
    ]);
});

<?php

use App\Models\User;
use App\Models\Invoice;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Enums\InvoiceStatus;
use Laravel\Sanctum\Sanctum;

test('authenticated user can list their invoices', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    $registration = EventRegistration::create([
        'booker_user_id' => $user->id,
        'event_id' => $event->id,
        'payment_status' => PaymentStatus::Unpaid,
        'status' => RegistrationStatus::Draft,
        'total_amount' => 1000
    ]);
    
    // One invoice is auto-created. Let's create a second one with version 2
    Invoice::create([
        'event_registration_id' => $registration->id, 
        'invoice_number' => 'INV-002', 
        'status' => InvoiceStatus::Issued, 
        'total_amount' => 1000, 
        'version' => 2, 
        'issued_at' => now(), 
        'due_at' => now()->addDays(7)
    ]);

    Sanctum::actingAs($user, ['*']);

    $response = $this->getJson('/api/v1/invoices');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('authenticated user can view specific invoice', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    $registration = EventRegistration::create([
        'booker_user_id' => $user->id,
        'event_id' => $event->id,
        'payment_status' => PaymentStatus::Unpaid,
        'status' => RegistrationStatus::Draft,
        'total_amount' => 1000
    ]);
    
    $invoice = $registration->invoices()->first();

    Sanctum::actingAs($user, ['*']);

    $response = $this->getJson("/api/v1/invoices/{$invoice->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $invoice->id);
});

test('user cannot view someone elses invoice', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $event = Event::factory()->create();
    $registration = EventRegistration::create([
        'booker_user_id' => $otherUser->id,
        'event_id' => $event->id,
        'payment_status' => PaymentStatus::Unpaid,
        'status' => RegistrationStatus::Draft,
        'total_amount' => 1000
    ]);
    
    $invoice = $registration->invoices()->first();

    Sanctum::actingAs($user, ['*']);

    $response = $this->getJson("/api/v1/invoices/{$invoice->id}");

    $response->assertStatus(403);
});

test('authenticated user can get invoice download link', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    $registration = EventRegistration::create([
        'booker_user_id' => $user->id,
        'event_id' => $event->id,
        'payment_status' => PaymentStatus::Unpaid,
        'status' => RegistrationStatus::Draft,
        'total_amount' => 1000
    ]);
    
    $invoice = $registration->invoices()->first();

    Sanctum::actingAs($user, ['*']);

    $response = $this->getJson("/api/v1/invoices/{$invoice->id}/download");

    $response->assertStatus(200)
        ->assertJsonStructure(['download_url']);
});

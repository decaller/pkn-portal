<?php

use App\Console\Commands\SendParticipantSlotRemindersCommand;
use App\Console\Commands\SendPaymentReminderNotificationsCommand;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\EmptyParticipantSpotReminderNotification;
use App\Notifications\NewEventOpenForRegistrationNotification;
use App\Notifications\PastEventPostedOrUpdatedNotification;
use App\Notifications\PaymentApprovedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('does not send a payment upload reminder when a registration is created', function () {
    Notification::fake();

    $user = User::factory()->create();
    $organization = Organization::factory()->create(['admin_user_id' => $user->id]);
    $user->organizations()->attach($organization, ['role' => 'admin']);
    $event = Event::factory()->create(['allow_registration' => true, 'event_date' => now()->addDays(10)]);

    EventRegistration::create([
        'event_id' => $event->id,
        'organization_id' => $organization->id,
        'booker_user_id' => $user->id,
        'package_breakdown' => [['package_name' => 'General', 'quantity' => 1, 'unit_price' => 100]],
        'total_amount' => 100,
        'status' => RegistrationStatus::Draft,
        'payment_status' => PaymentStatus::Unpaid,
    ]);

    Notification::assertNothingSent();
});

it('sends a payment approved notification when the gateway marks a registration as paid', function () {
    Notification::fake();

    $user = User::factory()->create();
    $organization = Organization::factory()->create(['admin_user_id' => $user->id]);
    $user->organizations()->attach($organization, ['role' => 'admin']);
    $event = Event::factory()->create(['allow_registration' => true, 'event_date' => now()->addDays(10)]);

    $registration = EventRegistration::create([
        'event_id' => $event->id,
        'organization_id' => $organization->id,
        'booker_user_id' => $user->id,
        'package_breakdown' => [['package_name' => 'General', 'quantity' => 1, 'unit_price' => 100]],
        'total_amount' => 100,
        'status' => RegistrationStatus::Draft,
        'payment_status' => PaymentStatus::Unpaid,
    ]);

    Notification::fake(); // Reset — only care about payment approved

    $registration->markPaidFromGateway();

    Notification::assertSentTo($user, PaymentApprovedNotification::class);
});

// ─── NewEventOpenForRegistration: sent when allow_registration is toggled true ───

it('sends a new event open notification to all org users when registration is enabled', function () {
    Notification::fake();

    $user = User::factory()->create();
    $organization = Organization::factory()->create(['admin_user_id' => $user->id]);
    $user->organizations()->attach($organization, ['role' => 'admin']);

    $event = Event::factory()->create(['allow_registration' => false, 'event_date' => now()->addDays(10)]);

    $event->update(['allow_registration' => true]);

    Notification::assertSentTo($user, NewEventOpenForRegistrationNotification::class);
});

it('keeps the legacy payment reminder command as a no-op', function () {
    Notification::fake();

    $this->artisan(SendPaymentReminderNotificationsCommand::class)
        ->assertExitCode(0);

    Notification::assertNothingSent();
});

// ─── SendParticipantSlotRemindersCommand ───

it('sends participant slot reminders via the scheduled command when spots are empty', function () {
    Notification::fake();

    $user = User::factory()->create();
    $organization = Organization::factory()->create(['admin_user_id' => $user->id]);
    $user->organizations()->attach($organization, ['role' => 'admin']);
    $event = Event::factory()->create(['allow_registration' => true, 'event_date' => now()->addDays(10)]);

    // Create registration with 2 expected participants, but no actual participants
    EventRegistration::create([
        'event_id' => $event->id,
        'organization_id' => $organization->id,
        'booker_user_id' => $user->id,
        'package_breakdown' => [['package_name' => 'General', 'quantity' => 2, 'unit_price' => 100]],
        'total_amount' => 100,
        'status' => RegistrationStatus::Draft,
        'payment_status' => PaymentStatus::Unpaid,
    ]);

    Notification::fake(); // Reset after creation

    $this->artisan(SendParticipantSlotRemindersCommand::class)
        ->assertExitCode(0);

    Notification::assertSentTo($user, EmptyParticipantSpotReminderNotification::class);
});

// ─── PastEventPostedOrUpdated: sent when a past event is published ───

it('sends a past event notification when an event with a past date is published', function () {
    Notification::fake();

    $user = User::factory()->create();
    $organization = Organization::factory()->create(['admin_user_id' => $user->id]);
    $user->organizations()->attach($organization, ['role' => 'admin']);

    $event = Event::factory()->create([
        'is_published' => false,
        'allow_registration' => false,
        'event_date' => now()->subDays(30),
    ]);

    $event->update(['is_published' => true]);

    Notification::assertSentTo($user, PastEventPostedOrUpdatedNotification::class);
});
